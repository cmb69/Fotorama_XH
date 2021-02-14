# Fotorama\_XH

Fotorama\_XH ermöglicht das Einbetten von [Fotorama](https://fotorama.io/)
Galerien auf CMSimple\_XH Seiten.
Das Plugin bietet keinerlei Bild-Upload-Möglichkeit,
sondern verwendet statt dessen Bilder aus dem Bilderordner von CMSimple\_XH
oder von irgendwo im World Wide Web (bislang wird nur JPEG unterstützt).
Jede Galerie kann individuell konfiguriert werden,
und jedes Bild kann eine zusätzliche Beschriftung erhalten.

- [Voraussetzungen](#voraussetzungen)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
    - [Vorbereiten einer Galerie](#vorbereiten-einer-galerie)
    - [Externe Bilder](#externe-bilder)
    - [Einbetten einer Galerie](#einbetten-einer-galerie)
- [Einschränkungen](#einschränkungen)
- [Fehlerbehebung](#fehlerbehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Fotorama\_XH ist ein Plugin für CMSimple\_XH ≥ 1.7.0.
Es benötigt PHP ≥ 5.3.0 mit den fileinfo, gd und simplexml Extensions.

## Download

Das [aktuelle Release](https://github.com/cmb69/fotorama_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple\_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
1. Laden Sie das ganze Verzeichnis `fotorama/` auf Ihren Server
   in das Plugin-Verzeichnis von CMSimple\_XH hoch.
1. Vergeben Sie falls nötig Schreibrechte für die Unterverzeichnisse
   `cache/`, `config/`, `css/` und `languages/`.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen
CMSimple\_XH-Plugins auch im Administrationsbereich der Website.
Gehen Sie zu `Plugins` → `Fotorama`.

Sie können die Voreinstellungen von Fotorama\_XH unter `Konfiguration` ändern.
Beim Überfahren der Hilfe-Icons mit der Maus
werden Hinweise zu den Einstellungen angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen.
Sie können die Sprachtexte in Ihre eigene Sprache übersetzen,
falls keine entsprechende Sprachdatei zur Verfügung steht,
oder diese Ihren Wünschen gemäß anpassen.

Das Aussehen von Fotorama\_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

### Vorbereiten einer Galerie

Zunächst müssen Sie einige Bilder in einen Unterordner
des Bilderordners von CMSimple\_XH hoch laden.
Sie können entweder den Dateibrowser von CMSimple\_XH
oder Ihren bevorzugten FTP-Client verwenden.
Dann müssen Sie die XML-Datei mit der Galerie-Definition im Backend erstellen.
Navigieren Sie zu `Plugins` → `Fotorama` → `Galerien`,
und verwenden Sie das Formular um eine erste XML-Datei
mit allen Bildern des gewählten Ordners zu erstellen.
Der Name der Galerie darf nur römische Kleinbuchstaben (`a`-`z`),
arabische Ziffern (`0`-`9`) und Bindestriche (`-`) enthalten.
Der Name der Galerie wird als Dateiname verwendet
(wobei `.xml` angehängt wird),
und die Datei wird im `content/` Ordner von CMSimple\_XH gespeichert.
Jede Sprache hat ihren eigenen Satz von Galerie-Definitionsdateien,
so dass Sie die Bildbeschriftungen übersetzen können.

Nachdem die XML-Datei erfolgreich erstellt wurde,
werden Sie zum Galerie-Editor weiter geleitet,
wo Sie die Feinabstimmung der Galerie vornehmen können,
indem Sie die XML-Datei bearbeiten.
Sie können `pic` Elemente entfernen und hinzufügen,
und deren Reihenfolge verändern.
Jedem `pic` Element kann optional ein `caption` Attribut (Beschriftung)
hinzugefügt werden, dessen Wert in der Galerie angezeigt wird;
die Beschriftung wird ebenfalls als `alt` Attribut des HTML `<img>` verwendet.
Sie können den Wert des `path` Attributs (Pfad) ändern,
aber Sie dürfen das Attribut nicht komplett entfernen.
Beachten Sie, dass Sie die ersten drei Zeilen der Datei
(die XML- und die Doctype-Deklaration) nicht ändern sollten.

Weiterhin können Sie dem `<gallery>` Element (Galerie) zusätzliche Attribute
(das `path` Attribute ist auf jeden Fall erforderlich) geben,
die die Funktionalität und das Aussehen der Galerie beeinflussen.
Folgende Attribute werden unterstützt:

- `width` und `ratio`:
  Diese Attribute geben die Breite bzw. das Seitenverhältnis der Galerie an.
  Die Breite ist entweder eine einfache Zahl,
  die die Breite in Pixeln angibt (z.B. `400`),
  oder ein Prozentsatz des verfügbaren horizontalen Platzes (z.B. `100%`),
  was besonders nützlich für responsive Layouts ist.
  Das Seitenverhältnis ist entweder ein Bruch (z.B. `400/300` oder `16/9`)
  oder eine Dezimalzahl (z.B. `1,3333`).
  Werden diese Attribute ausgelassen,
  dann werden Breite und Seitenverhältnis durch das erste Bild bestimmt.
  Beachten Sie, dass die Größe der Bilder angepasst wird,
  so dass diese zu Breite/Seitenverhältnis passen,
  damit es möglich ist, Bilder im Hoch- und Querformat
  in der selben Galerie ohne Verzerrung zu mischen.
- `nav`:
  Nur `thumbs` (Vorschaubild) ist erlaubt,
  wenn dieses Attribut angegeben wird.
  Das erweitert die Punkt-Navigation zu einer Vorschaubild-Navigation.
  Die erforderlichen Vorschaubilder werden bei Bedarf automatisch erzeugt,
  und im `cache/` Ordner des Plugins gespeichert.
- `fullscreen`:
  Dies erlaubt dem Besucher in die Vollbildansicht zu wechseln.
  Wählen Sie entweder `true`,
  was die Vollbildansicht auf das Browserfenster beschränkt,
  aber auch in älteren Browsern funktioniert,
  oder `native`, was den gesamten Bildschirm verwendet,
  wenn vom Browser unterstützt.
- `transition`:
  Entweder `slide` (die Voreinstellung, wenn das Attribut ausgelassen wird),
  `crossfade` oder `dissolve`.
  Letzteres ist vermutlich nur dann sinnvoll,
  wenn Sie Bilder haben, die sich nur leicht unterscheiden;
  ansonsten ist `crossfade` vorzuziehen.

Wird die Datei gespeichert, wird sie automatisch gegen die DTD validiert.
Das funktioniert nicht, wenn Sie offline arbeiten.
In diesem Fall können Sie die automatische Validierung
in der Konfiguration des Plugins deaktivieren.

### Externe Bilder

Es ist ebenfalls möglich externe Bilder
(d.h. Bilder außerhalb von Ihrem Bilderordner)
durch Angabe der vollständig qualifizierten URL des Bildes
als `path` des `<pic>` Elements anzuzeigen.
Wie in diesem Fall üblich ist zu beachten,
dass beispielsweise das Bild nicht verfügbar ist,
und unter Umständen rechtliche Einschränkungen gelten.
Beachten Sie, dass für externe Bilder keine Vorschaubilder generiert werden, 
sondern statt dessen ein Standard-Vorschaubild angezeigt wird,
das Sie durch Ersetzen von `plugins/fotorama/images/external.jpg`
mit einem Bild Ihrer Wahl ändern können.

Sie können externe Bilder und Bilder im Gallerieordner beliebig mischen.

### Einbetten einer Galerie

Um eine Galerie auf einer Seite einzubinden, schreiben Sie einfach:

    {{{fotorama('%NAME%')}}}

wobei `%NAME%` der Name der Galerie ist, z.B.

    {{{fotorama('urlaub')}}}

## Einschränkungen

Damit die Galerien *voll* funktionstüchtig sind,
muss JavaScript im Browser des Besuchers aktiviert sein.

## Fehlerbehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/fotorama_xh/issues)
oder im [CMSimple\_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Fotorama\_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Fotorama\_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Fotorama\_XH erhalten haben. Falls nicht, siehe
<https://www.gnu.org/licenses/>.

Copyright 2015-2021 Christoph M. Becker

## Danksagung

Dieses Plugin verwendet [Fotorama](https://fotorama.io/)
zur Anzeige der Galerien.
Vielen Dank an Artem Polikarpov, dem Entwickler dieser Bibliothek,
für seine großartige Arbeit, und für die Veröffentlichung unter MIT-Lizenz.

Das Pluginlogo wurde von [Everaldo Coelho](https://www.everaldo.com/) gestaltet.
Vielen Dank für die Veröffentlichung unter LGPL.
Das Plugin verwendet ebenfalls Icons aus dem
[Oxygen Icon-Set](http://www.oxygen-icons.org/).
Vielen Dank für die Veröffentlichung dieses Icon-Sets unter GPL.

Vielen Dank an die Community im
[CMSimple\_XH Forum](https://www.cmsimpleforum.com/)
für Hinweise, Anregungen und das Testen.
Besonders möchte ich *Traktorist* für das frühe und wertvolle
Feedback zur ersten Beta-Version danken.

Und zu guter letzt vielen Dank an
[Peter Harteg](https://www.harteg.dk/), den „Vater“ von CMSimple,
und allen Entwicklern von [CMSimple\_XH](https://www.cmsimple-xh.org/de/)
ohne die es dieses phantastische CMS nicht gäbe.
