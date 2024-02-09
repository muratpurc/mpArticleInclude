# CONTENIDO CMS Modul mp_article_include zum Einbinden von CONTENIDO Artikeln

## Beschreibung

Das Modul `mp_article_include` basiert auf der früheren Version des Moduls
Article Include (v1.0) aus dem CONTENIDO Forum.

Mit diesem Modul lassen sich Artikel einer CONTENIDO Seite einbinden.

Um den Inhalt eines gewünschten Artikels zu erhalten, wird eine reguläre HTTP
Anfrage zum Artikel abgesetzt. Aus der erhaltenen Ausgabe extrahiert das Modul
den gewünschten Inhalt.

Das Ergebnis der HTTP-Anfrage wird aus Performancegründen gecacht. Ist der
Artikel aktueller als die gecachte Version, übernimmt das Modul die neue 
Artikelversion in den Cache. Damit der Cache korrekt funktioniert, sollten
der Web-Server und der Datenbank-Sever die gleichen Zeitzonen verwenden.

Artikel, die mit diesem Modul einzubinden sind, brauchen definierte Marker
im Layout, um den gewünschten Inhalt aus der Seite zwischen den beiden
Markern extrahieren und einbinden zu können. Das Modul ist nicht dafür
geeignet, um einzelne Content-Typen aus Artikeln zu extrahieren.

Das Originalmodul ist unter folgender Seite zu finden:
http://forum.contenido.org/viewtopic.php?t=24027&p=132951#p132951

----

## Voraussetzungen

- CONTENIDO >= 4.10.*
- PHP >= 7.1 und PHP < 8.2
- CONTENIDO Plugin "Mp Dev Tools"

Das benötigte Plugin (Package "mp_dev_tools.zip") von der [GitHub-Seite](https://github.com/muratpurc/CONTENIDO-plugin-mp_dev_tools/releases)
herunterladen und in CONTENIDO installieren.

----

## Installation/Verwendung

Die im Modulpackage enthaltenen Dateien/Sourcen sind wie im Folgenden beschrieben 
zu installieren.
Die Pfade zu den Sourcen (CSS, JS und Templates) können von Projekt zu Projekt 
unterschiedlich sein und sind bei Bedarf anzupassen. 
Bei der Installationsbeschreibung wird davon ausgegangen, dass CONTENIDO in das 
DocumentRoot-Verzeichnis eines Webservers installiert wurde und das 
Mandantenverzeichnis `cms` lautet.

### Modul

Das Modul (Package "mp_article_include.zip") von der [GitHub-Seite](https://github.com/muratpurc/mpArticleInclude/releases)
herunterladen und in CONTENIDO importieren.

Alternativ den Modulordner `mp_article_include` samt aller Inhalte in das Modulverzeichnis
des Mandanten `cms/data/modules` kopieren.
Danach sollte man im Backend die Funktion `Module synchronisieren` unter
`Style -> Module` ausführen.

### Marker im Layout

In gewünschten Layouts die Marker `<!--start:content-->` und `<!--end:content-->`
setzen, um die Inhalte zwischen den Markern extrahieren zu können. Die Marker
sollten HTML-Kommentare sein, damit sie die Ausgabe der Artikel nicht
beeinträchtigen, z. B.:

```
...
<!--start:content-->
    <div id="content">
        <container id="123" name="content" types="content" mode="optional" default="">content</container>
        <container id="124" name="content" types="content" mode="optional" default="">content</container>
    </div>
<!--end:content-->
...
```

### Einrichten des Moduls

Dieses Modul in einer Artikelvorlage einrichten, in der die Inhalte anderer Artikel
(die auf Layouts mit den Markern basieren) eingebunden werden sollen.

Einen Artikel erstellen, welches auf die Vorlage basiert.

In der Artikelkonfiguration die Kategorie und den Artikel wählen, aus der der
Inhalt eingebunden werden soll.

----

## Einstellungen

Vom Modul verwendete Einstellungen, die auf Benutzer-, Gruppen, Mandanten- oder
System-Ebene gesetzt werden können.

|Typ|Name|Beschreibung|Standardwert|
|---|----|------------|------------|
|module_mp_article_include|include_mode|Die unterschiedlichen Arten, mit denen die HTTP-Anfragen zum Einbinden der Artikel versendet werden können.<br/>Mögliche Werte sind:<br/>"curl", "fsockopen", "file_get_contents", oder "snoopy"||

----

## Changelog

**2023-03-16 mp_article_include 1.5.0 (für CONTENIDO 4.10.x)**
- change: PHP 8.1 Support
- change: Auswahl diverser Methoden zum Einbinden (HTTP-Anfragen) von Artikeln
- change: Cachen des eingebundenen Artikels
- change: Auf PHP 8 adaptierte Version von Snoopy
- change: Verwendung des CONTENIDO Plugins "Mp Dev Tools"

**2019-11-13 mp_article_include 1.4.0 (für CONTENIDO 4.9.x - 4.10.x)**
- change: Ordner "vendor" in "lib" umbenannt
- change: Update auf Snoopy 2.0.0 mit PHP 7.2 support

**2014-07-23 mp_article_include 1.3.3 (für CONTENIDO 4.9.x)**
- bugfix: Ermitteln der Länge des Ende-Markers

**2013-11-17 mp_article_include 1.3.2 (für CONTENIDO 4.9.x)**
- change: Überarbeiten der Moduleingabe und -ausgabe
- change: Englische Übersetzungen

**2013-09-19 mp_article_include 1.3.1 (für CONTENIDO 4.9.x)**
- change: Inhalt inkl. Ende-Marker extrahieren

**2013-09-10 mp_article_include 1.3 (für CONTENIDO 4.9.x)**
- Erste Veröffentlichung des mp_article_include Moduls

----

## mp_article_include Modul Links

- [GitHub-Seite](https://github.com/muratpurc/mpArticleInclude/releases)

- [CONTENIDO Forum unter "CONTENIDO 4.10 -> Module und Plugins"](https://forum.contenido.org/viewtopic.php?t=43773)

- [CONTENIDO Forum unter "CONTENIDO 4.9 -> Module und Plugins"](http://forum.contenido.org/viewtopic.php?t=34110)
