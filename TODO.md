# Aktuell

## Funktionen

1. Wochenansicht soll Standart werden: ✓
Die Wochenansicht soll sofort erscheinen und die Tagesansicht mit extra Klick erreichbar sein.

2. Klarere Ankündigungen: ✓
Der ausklappbare Ankündigungspunkt auf der Kurzinfo Seite sollte beim Laden bereits ausgeklappt sein. Alternativ könnte man die Eintragung in der Einrichtung zur Voraussetzung für das Anlegen von Terminen machen. Dann würden Ankündigungen per Rundmail ohne großen Aufwand alle Nutzer erreichen.

3. Drucken von Kommentaren: ✓
Das Kommentarfeld eines Termins wird in der ausgedruckten Ansicht nicht dargestellt. Diese Funktion ist wichtig für die Modellbauwerkstatt, da die Adminaccounts manchmal Termine für Studenten anlegen. In diesem Fall geht der Name des Termineigentümers nur aus dem Kommentarfeld hervor und muss auf den ausgedruckten DIN A5 Zetteln der Werkstatt klar ersichtlich sein.  

Die Kommentare können beim Druck in die Terminblöcke geschrieben werden. Eventuell erfordert das eine Beschränkung der Textlänge.

4. Übertragung der Auftragsdaten:
Gewünscht ist ein Uploadfeld im Termin das von Studierenden zum Upload von Auftragsdateien an die Drucker, etc. genutzt werden kann. Später kann das Personal in der Werkstatt die Datei aus dem Termin downloaden und den Auftrag ausführen. Alternativ kann auch im Gerät selber alle Dateien vom aktuellen Tag runtergeladen werden.

Hier könnte man das Cloud Storage System der TU nutzen. Eine Funktion in der man einen der downloadlinks hinterlegen kann die in der Webansicht generiert werden können könnte so etwas leisten ohne Stud.IP zusätzlich zu belasten. 

5. Anlegen von dauerhaften Terminen:
Ein regelmäßiger Termin ähnlich wie in einem Kalender. Rhythmus wäre täglich, wöchentlich, zweiwöchentlich und monatlich. Termine die nicht gebraucht werden müssen sperrbar sein. Der Eintrag des Termins soll mit der gleichen Zeitmaske wie bei den täglichen Öffnungszeiten der Arbeitsplätze geschehen.  

Neue Kategorie von Termin?  

6. Bessere Übersicht der Arbeitsplätze:
Die Darstellung der Arbeitsplätze soll besser Gegliedert werden. Es soll möglich sein Kategorien und Objektklassen als ausklappbare Überschriften zu erzeugen. Die erstellten Arbeitsplätze sollen als Instanzen der Objektklassen auftauchen. Also Kategorie -> Objektklasse -> Objektinstanz (bisheriger Arbeitsplatz). 

Benötigen Kategorien und Objektklassen eigene Datenbankeinträge? Wenn man sie ohne implementiert sind sie bei einem Stromausfall weg - aber ist das realistisch beim Produktivsystem? Wie bildet man die resultierende Baumstruktur des Systems ab - Bei Datenbankeinträgen könnte man mit Fremdschlüsseln arbeiten.

7. Neue Kategorie - Arbeitsgerät:
Alternative Klasse von Arbeitsplätzen mit eigener Klasse von Terminen. Objektklasse mit fester Anzahl von Instanzen die so lange verliehen werden kann bis es keine Instanzen mehr gibt. Verleih soll weniger kleinteilig sein. Also mindestenz Blöcke von 4 Stunden, eventuell auch ganze Tage.

Muss so entwickelt werden das alle bisherigen Methoden mit beiden Klassen von Geräten und Terminen arbeiten können.

# Langfristig

## Funktionen
1. Löschen des eigenen Termins:
Das Löschen des eigenen Termins noch vor dem Startzeitpunkt ist zu kurzfristig möglich. Minuten vor dem Termin kann niemand mehr darauf reagieren - Termin neuvergeben etc. Dies sollte schon viel früher blockiert werden. Wunsch wäre ein Werktag.

2. Größe der Wochenansicht: ✓
Wochenansicht und Tagesansicht sind sehr kleinteilig gehalten. Die Formatierung könnte größere Elemente haben. Moderne Monitore lassen mehr Spielraum als wir nutzen.

Größere Formatierung mit mehr Platz für die Slots wäre besser. 

3. Mailanhang:
Momentan in Bezug auf erlaubte Dateitypen und Größe beschränkt. Gewünscht wären 500MB mit PDF, TIFF, DWG, DXF, STL und SAT Endungen möglich und automatischer Löschung nach 4 Tagen.  