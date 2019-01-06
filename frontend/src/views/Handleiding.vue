<template>
    <div class="voorbeelcode">
        <h1>Handleiding</h1>

        <p>Ieder groepje heeft een voorgeconfigureerde webserver tot zijn beschikking met daarop een LAMP-stack.
            Omdat het werken met zo'n server nieuw is voor de meesten kun je hier wat tips vinden. Gedurende het project
            zal onderstaande pagina worden aangevuld, dus kom hier vooral nog een keer terug.</p>

        <h3>SSH-client</h3>
        <p>Om wijzigingen op je server aan te kunnen brengen zul je moeten inloggen, dit kan doormiddel van een "Secure Shell", ofwel SSH.
            Onder Linux en op je Mac kun je een terminal openen en vervolgens het commando <tt>ssh</tt> uitvoeren
            (hoe je precies moet inloggen kun je op deze website vinden als je bent ingelogd).
            Als je Windows gebruikt kun je <a href="https://www.chiark.greenend.org.uk/~sgtatham/putty/latest.html">PuTTY</a> downloaden
            of gebruikmaken van het <a href="https://docs.microsoft.com/en-us/windows/wsl/install-win10">Windows Subsystem for Linux</a>.
            Nadat je bent ingelogd kun je gebruik maken van je terminal om interactie te hebben met de server, als je hier
            geen ervaring mee hebt helpt het vooral om een tutorial te volgen, daarover kun je meer vinden op de
            <a href="https://linux.datanose.nl/index.php/Shell">BYOD-wiki</a>. Als je het beu bent om je wachtwoord telkens
            in te moeten tikken, kijk dan vooral eens naar ssh-keys en het <tt>ssh-copy-id</tt>-commando.
        </p>

        <h3>Sudo en root</h3>
        <p> <img src=" https://imgs.xkcd.com/comics/sandwich.png" style="float:right; width:250px; margin-left:30px; margin-right: 10px;" />
            Iedere gebruiker heeft root-rechten op de server en kan deze via het <tt>sudo</tt>-commando verkrijgen.
            Als root-gebruiker ben je de opper-sysadmin van het systeem en kun je met één commando je volledige project en de serverconfiguratie
            onherroepelijk vernietigen. De servers zijn zo geconfigureerd dat het gebruiken van het root-account <i>in principe</i>
            niet nodig is. Kijk ook altijd uit met commando's die je van internet kopieert (ongeacht of je het root-account wel of niet gebruikt),
            er zijn wat internettrollen die je expres je systeem willen laten beschadigen. Stel jezelf vooral de volgende vragen:
        <ol style="margin-left:25px; margin-top: 5px; margin-bottom:5px;">
            <li>Moet ik dit commando echt als root uitvoeren?</li>
            <li>Weet ik wat ik aan het doen ben?</li>
            <li>Heb ik een backup als het misgaat?</li>
        </ol>
        Als je deze drie vragen met ja kunt beantwoorden, dan mag je het voorzichtig proberen. Als je twijfelt,
        neem dan eerst contact op met de serverondersteuning.
        </p>

        <h3>Bestanden op je server zetten</h3>
        <p>
            Het ontwikkelen van een website met een groepje vraagt niet alleen om skills in het webprogrammeren, ook zul
            je de geschreven code telkens moeten samenvoegen en verplaatsen van een ontwikkelplaats naar je uiteindelijke website.
            Het is misschien geen primair leerdoel van het vak, maar het loont om tijd te investeren in het goed opzetten
            van deze "workflow". Want zeg nou zelf, bij elke wijziging die je hebt geschreven een bestand handmatig moeten
            copy-pasten 4 weken lang, daar zit je niet op te wachten toch? Hieronder enkele tips:
        <ol style="margin-left:25px; margin-top: 5px; margin-bottom:5px;">
            <li><u>Git</u>: dit is het moment om git te leren gebruiken. Jullie hebben allemaal een GitLab-account
                van de UvA. Je kunt lokaal (op je eigen laptop) ontwikkelen of in je home-directory op de server, je wijzigingen
                committen en pushen. Je website updaten is dan gewoon een kwestie van onder <u><i>/var/www/html</i></u> een git-pull uitvoeren.
                Daar kun je trouwens ook <i>git hooks</i> voor gebruiken als je echt fanatiek bent, cronjobs zijn niet oké.</li>
            <li><u>SSHFS</u>: met sshfs kun je een map op een server verbinden met (of <i>mounten</i> naar) een map op je eigen computer.
                Als je zelf geen ontwikkelomgeving wilt opzetten is dit de manier om te zorgen dat de wijzigingen in je editor direct op de server komen.
                Je kunt <a href="https://www.digitalocean.com/community/tutorials/how-to-use-sshfs-to-mount-remote-file-systems-over-ssh">hier</a> meer
                informatie vinden over SSHFS. <u><i>Protip:</i></u> unmount voordat je je laptop dichtklapt, anders wil je laptop nog wel eens
                moeilijk doen als je hem weer openklapt elders.</li>
            <li><u>SFTP:</u> er zijn talloze editors met ondersteuning voor SFTP of FTPS (beiden beschikbaar op je server), bijvoorbeeld
                <a href="https://notepad-plus-plus.org/">Notepad++</a> voor Windows-gebruikers.</li>
        </ol>
        </p>

        <h3>Een eigen domeinnaam koppelen</h3>
        <p>
            Leuk zo'n UvA-domeinnaam, maar welk sociaal netwerk heet er nou agileXYZ? Je kunt ook vrij eenvoudig en
            voor niet al te veel geld een domeinnaam koppelen aan je server èn HTTPS-inschakelen. Zorg er allereerst
            voor dat je domeinnaam een DNS A-record heeft naar het ip adres van je server. Je kunt DNS-records opzoeken met behulp van
            het commando <tt>dig</tt>, als je het commando niet hebt, apt-get dan even <u><a href="https://packages.ubuntu.com/bionic/dnsutils">dnsutils</a></u>.
        </p>
        <p>
            Tik vervolgens in je terminal in: <br/>
            <tt>dig A mijncoolesite.nl +short</tt> <br />
            De uitkomst hiervan moet hetzelfde ip-addres geven als: <br />
            <tt>dig A agileXYZ.science.uva.nl +short</tt>
        </p>

        <p>Als de ip-adressen overeen komen dan zou je op jouw eigen domeinnaam de website al moeten kunnen zien, maar je zal een foutmelding
            krijgen als je de site via https bezoekt, ook dit kunnen we vrij eenvoudig oplossen. <br />
            Maak een bestand aan op de server onder: <tt><u>/etc/apache2/sites-available/mijncoolesite.conf</u></tt>
            met de volgende inhoud:
        <pre>
&lt;VirtualHost *:80&gt;
    ServerName mijncoolesite.nl
    DocumentRoot /var/www/html
&lt;/VirtualHost&gt;
</pre>
        Vervolgens kun je deze site inschakelen met "<tt>sudo a2ensite mijncoolesite</tt>". Nu moet je alleen nog een
        ssl-certificaat genereren, hiervoor kun je gebruik maken van Let's Encrypt door het commando "<tt>sudo certbot</tt>"
        uit te voeren en het gegeven menu te doorlopen. Er worden je een hoop vragen gesteld, maar daar kom je vast uit.
        <u>Let goed op</u> dat je niet via Let's encrypt een certificaat aanvraagt
        voor de agileXYZ-domeinnaam, hier hebben wij al een certificaat van de UvA voor geïnstalleerd.
        </p>


    </div>
</template>

<script lang="ts">
    import Vue from 'vue';
    import { AxiosResponse } from 'axios';
    export default Vue.extend({
        name: 'voorbeelcode',

        computed: {
            course(): object {
                return this.$store.state.course;
            },
            examples_site_url(): string {
                return 'https://' + this.$store.state.course.examples_site;
            },
        },
    });
</script>

<style scoped>
    p {
        margin-bottom: 10px;
    }
</style>