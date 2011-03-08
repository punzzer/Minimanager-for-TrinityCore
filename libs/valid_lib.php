<?php
/*
 *  Copyright (C) 2010-2011  TrinityScripts <http://www.trinityscripts.xe.cx/>
 *
 *  This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

//#############################################################################
//making sure the input string contains only [A-Z][a-z][0-9]-_ chars.
function valid_alphabetic($srting)
{
    if (ereg('[^a-zA-Z0-9_-]{1,}', $srting))
        return false;
    else
        return true;
}


//#############################################################################
//testing given mail
function valid_email($email='')
{
    global $validate_mail_host;
    // checks proper syntax
    if (preg_match( '/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/', $email))
    {
        if ($validate_mail_host)
        {
            // gets domain name
            list($username,$domain) = split('@',$email);
            // checks for if MX records in the DNS
            $mxhosts = array();
            if (getmxrr($domain, $mxhosts))
            {
                // mx records found
                foreach ($mxhosts as $host)
                {
                    if (fsockopen($host,25,$errno,$errstr,7))
                        return true;
                }
                return false;
            }
            else
            {
                // no mx records, ok to check domain
                if (fsockopen($domain,25,$errno,$errstr,7))
                    return true;
                else
                    return false;
            }
        }
        else
            return true;
    }
    else
        return false;
}


//php under win does not support getmxrr()  function - so heres workaround
if (function_exists ('getmxrr') );
else
{
    function getmxrr($hostname, &$mxhosts)
    {
        $mxhosts = array();
        exec('%SYSTEMDIRECTORY%\nslookup.exe -q=mx '.escapeshellarg($hostname), $result_arr);
        foreach($result_arr as $line)
        {
            if (preg_match('/.*mail exchanger = (.*)/', $line, $matches))
                $mxhosts[] = $matches[1];
        }
        return( count($mxhosts) > 0 );
    }
}

//escape strings for use in SQL-Querys to prevent SQL-Injection
function cleanSQL($string)
{
    if(get_magic_quotes_gpc())  // prevents duplicate backslashes
        $string = stripslashes($string);

    if (phpversion() >= '4.3.0')
        $string = mysql_real_escape_string($string);
    else
        $string = mysql_escape_string($string);

    return $string;
}

?>