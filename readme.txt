CONTENIDO Modul mpArticle_Include 1.3 für CONTENIDO 4.9

################################################################################
TOC (Table of contents)

- BESCHREIBUNG
- INSTALLATION/VERWENDUNG
- CHANGELOG
- SCHLUSSBEMERKUNG


################################################################################
BESCHREIBUNG

Das Modul mpArticle_Include basiert auf der Version 1.0 des Moduls Article Include
aus dem CONTENIDO Forum.

Damit lassen sich über das Layout per Marker definierte Inhalte aus CONTENIDO
Seiten extrahieren und einbinden. Das Modul ist nicht dafür geeignet, um einzelne
Content-Typen aus Artikeln zu extrahieren.

Dabei wird mit Hilfe der Snoopy Klasse eine HTTP reguläre Anfrage zum Artikel
abgesetzt um dann den Inhalt der erhaltenen Antwort, also die Ausgabe zwischen
den Markern auszugeben.

Das Originalmodul ist unter folgender Seite zu finden:
http://forum.contenido.org/viewtopic.php?t=24027&p=132951#p132951


################################################################################
INSTALLATION/VERWENDUNG

Inhalt des Modulpackages in das Modulverzeichnis des Mandanten, z. B. in 
cms/data/modules/ entpacken.

In gewünschten Layouts die Marker <!--start:content--> und <!--end:content-->
setzen, um die Inhalte zwischen den Markern extrahieren zu können. Die Marker
sollten HTML-Kommentare sein, damit sie die reguläre Ausgabe der Artikel nicht
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

Dieses Modul in einer Artikelvorlage einrichten, in der die Inhalte anderer Artikel
(die auf Layouts mit den Markern basieren) eingebunden werden sollen.

Einen Artikel erstellen, die auf die Vorlage basiert.

In der Artikelkonfiguration die Kategorie und den Artikel wählen, aus der der
Inhalt eingebunden werden soll.


################################################################################
CHANGELOG

2013-09-10 mpArticle_Include 1.3
    * Erste Veröffentlichung des mpArticle_Include Moduls


################################################################################
SCHLUSSBEMERKUNG

Benutzung des Moduls auf eigene Gefahr!

Murat Purc, murat@purc.de
