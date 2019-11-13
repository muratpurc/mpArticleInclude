CONTENIDO Modul mpArticleInclude CONTENIDO CMS

################################################################################
TOC (Table of contents)

- BESCHREIBUNG
- INSTALLATION/VERWENDUNG
- CHANGELOG
- MPARTICLEINCLUDE MODUL LINKS
- SCHLUSSBEMERKUNG


################################################################################
BESCHREIBUNG

Das Modul mpArticleInclude basiert auf der Version 1.0 des Moduls Article Include
aus dem CONTENIDO Forum.

Damit lassen sich über das Layout per Marker definierte Inhalte aus CONTENIDO
Seiten extrahieren und einbinden. Das Modul ist nicht dafür geeignet, um einzelne
Content-Typen aus Artikeln zu extrahieren.

Um den Inhalt eines gewünschten Artikels zu erhalten, wird eine reguläre HTTP
Anfrage zum Artikel abgesetzt. Aus der erhaltenen Ausgabe extrahiert das Modul den
gewünschten Inhalt, zwischen den beiden Markern.

Das Originalmodul ist unter folgender Seite zu finden:
http://forum.contenido.org/viewtopic.php?t=24027&p=132951#p132951


################################################################################
INSTALLATION/VERWENDUNG

Die im Modulpackage enthaltenen Dateien/Sourcen sind wie im Folgenden beschrieben 
zu installieren.
Die Pfade zu den Sourcen (CSS, JS und Templates) können von Projekt zu Projekt 
unterschiedlich sein und sind bei Bedarf anzupassen. 
Bei der Installationsbeschreibung wird davon ausgegangen, dass CONTENIDO in das 
DocumentRoot-Verzeichnis eines Webservers installiert wurde und das 
Mandantenverzeichnis "cms/" ist.

1.) Modul
Den Modulordner "mp_article_include" samt aller Inhalte in das Modulverzeichnis
des Mandanten "cms/data/modules" kopieren.
Danach sollte man im Backend die Funktion "Module synchronisieren" unter
"Style -> Module" ausführen.

2.) Marker im Layout
In gewünschten Layouts die Marker <!--start:content--> und <!--end:content-->
setzen, um die Inhalte zwischen den Markern extrahieren zu können. Die Marker
sollten HTML-Kommentare sein, damit sie die Ausgabe der Artikel nicht
beeinträchtigen, z. B.:
[code]
...
<!--start:content-->
    <div id="content">
        <container id="123" name="content" types="content" mode="optional" default="">content</container>
        <container id="124" name="content" types="content" mode="optional" default="">content</container>
    </div>
<!--end:content-->
...
[/code]

3.) Einrichten des Moduls
Dieses Modul in einer Artikelvorlage einrichten, in der die Inhalte anderer Artikel
(die auf Layouts mit den Markern basieren) eingebunden werden sollen.

Einen Artikel erstellen, welches auf die Vorlage basiert.

In der Artikelkonfiguration die Kategorie und den Artikel wählen, aus der der
Inhalt eingebunden werden soll.


################################################################################
CHANGELOG

2019-11-13 mpArticleInclude 1.4.0 (für CONTENIDO 4.9.x - 4.10.x)
    * change: Ordner "vendor" in "lib" umbenannt
    * change: Update auf Snoopy 2.0.0 mit PHP 7.2 support

2014-07-23 mpArticleInclude 1.3.3 (für CONTENIDO 4.9.x)
    * bugfix: Ermitteln der Länge des Ende-Markers

2013-11-17 mpArticleInclude 1.3.2 (für CONTENIDO 4.9.x)
    * change: Überarbeiten der Moduleingabe und -ausgabe
    * change: Englische Übersetzungen

2013-09-19 mpArticle_Include 1.3.1 (für CONTENIDO 4.9.x)
    * change: Inhalt inkl. Ende-Marker extrahieren

2013-09-10 mpArticle_Include 1.3 (für CONTENIDO 4.9.x)
    * Erste Veröffentlichung des mpArticle_Include Moduls


################################################################################
MPARTICLEINCLUDE MODUL LINKS

mpArticleInclude Modul für CONTENIDO CMS 4.9.x:
http://www.purc.de/playground-cms_contenido_4.9-modul_mparticleinclude-a.129.html

mpArticleInclude (aka Article Include) im CONTENIDO Forum unter Module 4.9.x:
http://forum.contenido.org/viewtopic.php?t=34110


################################################################################
SCHLUSSBEMERKUNG

Benutzung des Moduls auf eigene Gefahr!

Murat Purç, murat@purc.de
