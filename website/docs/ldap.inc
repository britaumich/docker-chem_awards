<?php
$ldap = ldap_connect('ldap.itd.umich.edu');

function ldap_person($uniqname)
{
        $result = array('name' => NULL, 'phone' => NULL, 'cell' => NULL, 'title' => NULL);
        $uniqname = preg_replace('/@umich\.edu$/i', '', $uniqname);
        global $ldap;
        $sr = @ldap_search($ldap, 'ou=People,dc=umich,dc=edu', "(uid=$uniqname)",
                        array('displayName', 'cn', 'mobile', 'telephoneNumber', 'title'));

        $info = $sr ? ldap_get_entries($ldap, $sr) : FALSE;
        if (!$info) $info = array('count' => 0, 0 => array());

        $person = isset($info[0]) ? $info[0] : array();
        if (isset($person['displayName']) && $person['displayName']['count'] > 0)
        {
                $result['name'] = $person['displayName'][0];
        }
        elseif (isset($person['cn']) && $person['cn']['count'] > 0)
        {
                $result['name'] = $person['cn'][$person['cn']['count'] - 1];
        }

        if (isset($person['mobile']) && $person['mobile']['count'] > 0)
        {
                $result['cell'] = $person['mobile'][0];
        }

        if (isset($person['telephoneNumber']) &&
                $person['telephoneNumber']['count'] > 0)
        {
                $result['phone'] = $person['telephoneNumber'][0];
        }

        if (isset($person['title']) &&
                $person['title']['count'] > 0)
        {
                $result['title'] = $person['title'][0];
        }
        return $result;
}

# Returns an HTML quoted name for the given uniqname
function ldap_name($uniqname)
{
        $person = ldap_person($uniqname);
//echo '<pre>'; var_export($person); echo '</pre>';
        $name = is_null($person['name']) ? $uniqname : $person['name'];
        return htmlspecialchars($name, ENT_QUOTES);
}

function ldap_title($uniqname)
{
        $person = ldap_person($uniqname);
        $title = is_null($person['title']) ? $uniqname : $person['title'];
        return htmlspecialchars($title, ENT_QUOTES);
}
?>
