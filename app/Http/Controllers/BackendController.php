<?php

namespace App\Http\Controllers;


use App\Models\Server;
use App\Models\ServerDomain;

class BackendController extends Controller
{
    function getApache()
    {
        header("Content-Type: text/plain");

        foreach(ServerDomain::get() as $domain)
        {
            echo '<VirtualHost *:'.($domain->ssl ? "443" : "80").">\n";
            echo "    ServerName ".$domain->domain."\n";
            echo "    ProxyPass / ".($domain->ssl ? "https" : "http")."://".$domain->server->ip_address."/\n";
            echo "    ProxyPassReverse / ".($domain->ssl ? "https" : "http")."://".$domain->server->ip_address."/\n";
            if($domain->ssl)
            {
                echo "    SSLEngine on\n";
                echo "    SSLCertificateFile ".$domain->ssl_certificate."\n";
                echo "    SSLCertificateKeyFile ".$domain->ssl_private_key."\n";
                if($domain->ssl_ca_bundle)
                    echo "    SSLCACertificateFile ".$domain->ssl_ca_bundle."\n";

                echo "    SSLProxyEngine on\n";
                echo "    SSLProxyVerify none\n";
                echo "    SSLProxyCheckPeerCN off\n";
                echo "    SSLProxyCheckPeerName off\n";
                echo "    SSLProxyCheckPeerExpire off\n";
            }
            echo "</VirtualHost>\n\n";
        }

        // Temporary
        foreach(Server::get() as $server)
        {
            echo '<VirtualHost *:80>'."\n";
            echo "    ServerName ".$server->hostname."-mail.".env('WEBDB_URL')."\n";
            echo "    ProxyPass /messages ws://".$server->ip_address.":1080/messages\n";
            echo "    ProxyPassReverse /messages ws://".$server->ip_address.":1080/messages\n";
            echo "    ProxyPass / http://".$server->ip_address.":1080/\n";
            echo "    ProxyPassReverse / http://".$server->ip_address.":1080/\n";
            echo "</VirtualHost>\n\n";
        }


    }
}