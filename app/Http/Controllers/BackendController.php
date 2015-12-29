<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\ServerDomain;
use Illuminate\Http\Request;

class BackendController extends Controller
{
    function getApache(Request $request)
    {
        if($request->get('key') != env('WEBDB_BACKEND_KEY'))
            return;

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
    }

    function getApacheMailcatcher(Request $request)
    {
        if($request->get('key') != env('WEBDB_BACKEND_KEY'))
            return;

        header("Content-Type: text/plain");

        foreach(Server::get() as $server)
        {
            echo '<VirtualHost *:443>'."\n";
            echo "    ServerName ".$server->hostname."-mail.".env('WEBDB_URL')."\n";
            echo "    SSLEngine on\n";
            echo "    SSLCertificateFile ".env('WEBDB_MAILCATCHER_SSL_PATH')."cert.pem\n";
            echo "    SSLCertificateKeyFile ".env('WEBDB_MAILCATCHER_SSL_PATH')."privkey.pem\n";
            echo "    SSLCACertificateFile ".env('WEBDB_MAILCATCHER_SSL_PATH')."chain.pem\n";
            echo "    SSLProxyEngine on\n";
            echo "    SSLProxyVerify none\n";
            echo "    SSLProxyCheckPeerCN off\n";
            echo "    SSLProxyCheckPeerName off\n";
            echo "    SSLProxyCheckPeerExpire off\n";
            echo "    ProxyPass /messages-ws wss://".$server->ip_address.":81/messages-ws\n";
            echo "    ProxyPassReverse /messages-ws wss://".$server->ip_address.":81/messages-ws\n";
            echo "    ProxyPass / https://".$server->ip_address.":81/\n";
            echo "    ProxyPassReverse / https://".$server->ip_address.":81/\n";
            echo "</VirtualHost>\n\n";
        }
    }
}