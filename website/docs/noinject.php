<?php
# noinject.php by Brandon Bensel <benselb@umich.edu>, May 2009
# This library may be freely reproduced and modified without restriction.
# 
# How to use this:
# 1. Create a statement, using ?type? where quoted values should go.
#
#     $stmt = new SQLStatement("SELECT * FROM tbl WHERE when=?datetime?");
#          OR
#     $stmt = new SQLStatement("INSERT INTO tbl (`when`) VALUES (?datetime?)");
#
#    (use ?? if you want a literal question mark somewhere)
#
# 2. Execute the statement, giving it the database connection and values it
#    should use.
#
#     $res = $stmt->execute($dbconn, $_REQUEST["DATE"]);
#
# 3. Get any errors from $stmt; this will include both incorrect type errors and
#    the last MySQL error, depending on where the problem occurred.
#
#     if (!$res) die($stmt->error);
#
# If you just want to get a query string to use later, use NULL instead of
# a database connection:
#     $query = $stmt->execute(NULL, $_REQUEST["DATE"]);
#     if (!$query) die($stmt->error);
#     echo($query);  =>  SELECT * FROM table WHERE when='2009-12-25'
#
# A few things to note:
# * A NULL value always passes type checking. You need to check in your code if
#   a value might become NULL. There's no way for user input to give a NULL, so
#   it's assumed that you gave it and are okay with it.
#
# * It's not extremely efficient, since type checking and placeholder stuff
#   have obvious overhead. It shouldn't be extremely inefficient, just don't
#   expect it to be blazing fast if you're inserting thousands of rows at once.
#
# * Only basic checking is performed; it does not check if integers are the
#   right size for the field, only if they're valid integers.
#
# * Only a subset of the MySQL types are valid placeholder types due to the
#   above; smallint, etc. should be simply given as ?integer?. If you need
#   precise bounds checking, you should do it yourself.
#
# * It reverses the effect of magic_quotes_gpc when included; in other words,
#   it makes PHP behave as though it had never been enabled, and $_GET, $_POST,
#   etc. will no longer be pre-escaped.

# SQLquote($str) -> string
# Quotes $str for use in a SQL query.
function SQLquote($str)
{
	global $SQL_really_no_magic;
	if (is_null($str)) return "NULL";
	return "'" . addslashes($str) . "'";
}

define('SQL_NUM_RE', '-?\d+');
define('SQL_FLOAT_RE', '-?\d+(\.\d+)?([eE][+-]?\d+)');

$SQL_regexen = array(
	'varchar' => '.*', 'text' => '.*', 'char' => '.*', 'blob' => '.*',
	'any' => '.*', 'string' => '.*',
	'bit' => SQL_NUM_RE, 'bool' => SQL_NUM_RE, 'int' => SQL_NUM_RE,
	'integer' => SQL_NUM_RE, 'float' => SQL_FLOAT_RE,
	'double' => SQL_FLOAT_RE, 'decimal' => SQL_FLOAT_RE,
	'date' => '\d\d\d\d-\d\d-\d\d',
	'datetime' => '\d{4}-\d\d-\d\d \d\d:\d\d:\d\d',
	'timestamp' => '\d{4}-\d\d-\d\d \d\d:\d\d:\d\d',
	# Yes, time values like this are legal.
	'time' => '-?\d?\d\d:\d\d:\d\d', 'year' => '(\d\d)?\d\d'
);

class SQLPlaceholder
{
	var $type;

	function SQLPlaceholder($type)
	{
		$this->type = strtolower($type);
	}

	# Checks that the value matches the type, then returns it as a quoted
	# and escaped SQL string.
	#
	# If it fails to match the type, it returns FALSE.
	function check($value)
	{
		global $SQL_regexen;
		if (is_null($value)) return "NULL";
		if (isset($SQL_regexen[$this->type]))
		{
			$re = '/^' . $SQL_regexen[$this->type] . '$/si';
			if (!preg_match($re, $value))
			{
				return FALSE;
			}
		}
		else
		{
			die("Unknown SQL placeholder type: " . $this->type);
		}
		return SQLquote($value);
	}
}

# This class splits a string with format like:
#   INSERT INTO tbl (when, who) VALUES (?datetime?, ?varchar?)
# and allows you to execute it, escaping/quoting the values and checking that
# they make sense as the given type.
class SQLStatement
{
	var $query; # A list of strings and SQLPlaceholders
	var $nplaceholders;
	var $error;
	var $query_str; # If you need to get the query string after an error, it's here.
	var $original;
	var $badtype; # You can check this to see if an error was a type validation
	              # error. It holds which placeholder got a bad value, where
	              # 1 is the first placeholder, 2 the second, etc., or FALSE
	              # when the last error was a MySQL error.

	function SQLStatement($query_str)
	{
		$this->nplaceholders = 0;
		$this->error = "";
		$this->badtype = FALSE;
		$this->original = $query_str;
		$query = explode('?', $query_str);
		$placeholder = false;
		foreach ($query as $item)
		{
			if ($placeholder)
			{
				if (strlen($item) == 0)
				{
					# Turn ?? into a single ?
					$this->query[] = "?";
				}
				else
				{
					$this->query[] = new SQLPlaceholder($item);
					$this->nplaceholders += 1;
				}
			}
			else
			{
				$this->query[] = $item;
			}
			$placeholder = !$placeholder;
		}
	}

	# execute($db, $arg1, $arg2, ...)
	#
	# Executes the statement with the given values for the placeholders,
	# checking their types and finally executing on the given database if
	# $db is not NULL.
	#
	# If any of the arguments fail to match the type required by the
	#  placeholder, it returns FALSE.
	# If $db is NULL, it returns the statement it builds.
	# If $db is not NULL, it executes the statement and returns the result.
	function execute()
	{
		$this->badtype = FALSE;
		$this->error = "";
		$nargs = func_num_args();
		$db = func_get_arg(0);
		$arg = 1;
		if ($nargs != $this->nplaceholders + 1)
		{
			die("Incorrect arguments to SQLStatement::execute.");
		}
		$query_str = "";
		foreach ($this->query as $item)
		{
			if (is_string($item))
			{
				$query_str .= $item;
			}
			else
			{
				$qitem = $item->check(func_get_arg($arg));
				if ($qitem === FALSE)
				{
					$this->error = "Incorrect type in argument $arg to statement: " .
						$this->original . "\nValue: " . func_get_arg($arg);
					$this->badtype = $arg;
					return FALSE;
				}
				$arg += 1;
				$query_str .= $qitem;
			}
		}

		if (is_null($db))
		{
			return $query_str;
		}
		else
		{
			$this->query_str = $query_str;
			$res = mysqli_query($db, $query_str);
			if ($res === FALSE) $this->error = mysqli_error($db);
			return $res;
		}
	}
}

function prepare($s)
{
	return new SQLStatement($s);
}

# The following is from http://us2.php.net/manual/en/security.magicquotes.disabling.php 
# It reverses the effect of magic_quotes_gpc.
if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value)
	{
		$value = is_array($value) ?
				array_map('stripslashes_deep', $value) :
				stripslashes($value);

		return $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

function cleanUserInput($db, $input) {	
	// Escape if not a number
	if (!is_numeric($input)) {
		$input = mysqli_real_escape_string($db, $input);
	}
	
	return $input;
}
?>
