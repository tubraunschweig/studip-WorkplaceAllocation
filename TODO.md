# Aktuell

## Code

1.  Verwendung der SimpleORMap Klasse für die Datenbankabfragen
    Die Datenbankabfragen wären einfacher mit der hierfür vorgesehenen SimpleORMap Klasse zu implementieren.

2.  Verwendung des Trails MVC Frameworks
    Stud.IP verfügt über ein fertiges MVC Framework. Es wäre besser dieses zu verwenden anstatt der alten Methoden im Plug-in.

## Funktionen

1.  Wochenansicht soll Standart werden: ✓
    Die Wochenansicht soll sofort erscheinen und die Tagesansicht mit extra Klick erreichbar sein.

2.  Klarere Ankündigungen: ✓
    Der ausklappbare Ankündigungspunkt auf der Kurzinfo Seite sollte beim Laden bereits ausgeklappt sein. Alternativ könnte man die Eintragung in der Einrichtung zur Voraussetzung für das Anlegen von Terminen machen. Dann würden Ankündigungen per Rundmail ohne großen Aufwand alle Nutzer erreichen.

3.  Drucken von Kommentaren: ✓
    Das Kommentarfeld eines Termins wird in der ausgedruckten Ansicht nicht dargestellt. Diese Funktion ist wichtig für die Modellbauwerkstatt, da die Adminaccounts manchmal Termine für Studenten anlegen. In diesem Fall geht der Name des Termineigentümers nur aus dem Kommentarfeld hervor und muss auf den ausgedruckten DIN A5 Zetteln der Werkstatt klar ersichtlich sein.  

Die Kommentare können beim Druck in die Terminblöcke geschrieben werden. Eventuell erfordert das eine Beschränkung der Textlänge.

4.  Übertragung der Auftragsdaten:
    Gewünscht ist ein Uploadfeld im Termin das von Studierenden zum Upload von Auftragsdateien an die Drucker, etc. genutzt werden kann. Später kann das Personal in der Werkstatt die Datei aus dem Termin downloaden und den Auftrag ausführen. Alternativ kann auch im Gerät selber alle Dateien vom aktuellen Tag runtergeladen werden.

Hier könnte man das Cloud Storage System der TU nutzen. Eine Funktion in der man einen der downloadlinks hinterlegen kann die in der Webansicht generiert werden können könnte so etwas leisten ohne Stud.IP zusätzlich zu belasten.

5.  Anlegen von dauerhaften Terminen:
    Ein regelmäßiger Termin ähnlich wie in einem Kalender. Rhythmus wäre täglich, wöchentlich, zweiwöchentlich und monatlich. Termine die nicht gebraucht werden müssen sperrbar sein. Der Eintrag des Termins soll mit der gleichen Zeitmaske wie bei den täglichen Öffnungszeiten der Arbeitsplätze geschehen.  

Neue Kategorie von Termin?  

6.  Bessere Übersicht der Arbeitsplätze:
    Die Darstellung der Arbeitsplätze soll besser Gegliedert werden. Es soll möglich sein Kategorien und Objekttypen als ausklappbare Überschriften zu erzeugen. Die erstellten Arbeitsplätze sollen als Instanzen der Objekttypen auftauchen. Also Kategorie -> Objekttyp -> Objekt (bisheriger Arbeitsplatz).

7.  Neue Kategorie - Arbeitsgerät:
    Alternative Klasse von Arbeitsplätzen mit eigener Klasse von Terminen. Objektklasse mit fester Anzahl von Instanzen die so lange verliehen werden kann bis es keine Instanzen mehr gibt.

Der Verleih dieser Geräte soll weniger kleinteilig sein. Also ausleihbar für ganze Tage. Folglich ist bei diesen Geräten auch eine andere Seitendarstellung ohne Uhrzeiten notwendig. Diese Geräte müssen dann auch für mehrere Tage oder sogar Wochen ausleihbar sein.

Die Geräte benötigen Checkboxes mit "abgeholt" und "zurückgegeben" die von Mitarbeitern angekreuzt werden kann. Es könnte auch sinnvoll sein beim Verstreichen der entsprechenden Zeitpunkte automatisch Benachrichtigungen zu senden.

Eine Funktion mit der Studenten ihre entliehenen Geräte unter bestimmten Umständen verlängern könnten wäre auch nützlich. Wenn am Tag der Rückgabe noch Geräte verfügbar sind? Hat jedoch geringere Priorität.

Übersicht als Gantt-Diagramm für Mitarbeiter? Für Studenten so eine Monats-Kalenderansicht mit Anzahl der freien Geräte für jeden Tag eingetragen?

Muss so entwickelt werden das alle bisherigen Methoden mit beiden Klassen von Geräten und Terminen arbeiten können.

Idee:

Institute -> Category -> ObjectType -> Workplace (alt)
Institute -> Category -> ObjectType -> WorkTool -> WTInstance (neu)

-   Kategorie-Klasse Category

    -   Variablen
          id -> id der Klasseninstanz für Relationen
          name -> Name der Kategorie
          description -> Beschreibung der Kategorie
          institute -> zugehörige Einrichtung
          contextId -> parent - cid aus GET parameter
    -   Methoden
          getId
          getName
          getDescription
          getInstitute
          getContextId
          getMembers -> Übergibt alle ObjectType Objekte mit Id der Category als parent Id

          setName
          setDescription
          deleteCategory -> Löscht den Eintrag der Categoy, alle ObjectType, alle Worktools und alle WTInstance in DB
          addMember -> Ruft die newObjectType Methode der ObjectType Klasse auf und übergibt eigene Id

          static

          getCategory -> Gibt Category Objekt mit übergebener Id zurück
          newCategory -> Erstellt Eintrag in DB & gibt Category Objekt zurück
          getCategoriesByContext -> Gibt alle zum übergebenen Context gehörenden Category Objekte zurück

-   Objekttyp-Klasse ObjectType

    -   Variablen
          id -> ID der Klasseninstanz für Relationen
          name -> Name des Objekttyps
          description -> Beschreibung des Objekttyps
          type -> Handelt es sich um Arbeitsplätze (alt) oder ausleihbare Arbeitsgeräte (neu)
          categoryId -> parent - zugehörige Kategorie = id der Kategorie
    -   Methoden
          getId
          getName
          getDescription
          getType
          getCategoryId
          getMembers -> Übergibt alle WorkTool Objekte mit Id des ObjectType als parent Id

          setName
          setDescription
          setType -> Sollte nur bei Erstellung von newObjectType genutzt werden
          deleteObjectType -> Löscht den Eintrag des ObjectType, alle Worktools und alle WTInstance in DB
          addMember -> Ruft die newWorkTool Methode der WorkTool Klasse auf und übergibt eigene Id

          static

          getObjectType -> Gibt ObjectType Objekt mit übergebener Id zurück
          newObjectType -> Erstellt Eintrag in DB & gibt ObjectType Objekt zurück - Von der Category Klasse's addMember Methode genutzt

-   Objekt-Klasse WorkTool

    -   Variablen
          id -> ID der Klasseninstanz für Relationen
          name -> Name des Objektes
          description -> Beschreibung des Objektes
          active -> Ausleihbar?
          contextId -> contextID cid wie bei Kategorie für direkten Zugriff
          rule -> Regelobjekt wie bei den Arbeitsplätzen
          objectTypeId -> parent - zugehöriger Objekttyp = id des Objekttyp
    -   Methoden
          getId
          getName
          getDescription
          isActive
          getContextId
          getRule
          getObjectTypeId
          getMembers -> Übergibt alle WTInstance Objekte mit Id des WorkTools als parent Id
          countFreeByDay -> Gibt die Anzahl der am übergebenen Tag nicht belegten Instanzen zurück
          getBookedByDay -> Gibt die am übergebenen Tag belegten Instanzen zurück

          setName
          setDescription
          activate
          deactivate
          createRule
          deleteWorkTool -> Löscht den Eintrag des Worktools und alle WTInstance in DB
          addMember -> Ruft die newWTInstance Methode der Instanzen Klasse auf und übergibt eigene Id
          bookTool -> Checkt ob nicht belegte Instanzen verfügbar sind, öffnet Dialog für Auswahl der Zeitspanne
          freeTool -> Macht Instanzen mit returned = true wieder frei - sollte mit cronjob automatisiert werden

          static

          getWorkTool -> Gibt WorkTool Objekt mit übergebener Id zurück
          newWorkTool -> Erstellt Eintrag in DB & gibt Worktool Objekt zurück - Von der ObjectType Klasse's addMember Methode genutzt

-   Objektinstanz-Klasse WTInstance

    -   Variablen
          id -> ID der Klasseninstanz für Relationen
          owner -> User der Instanz ausgeliehen hat oder null - Von der WorkTool Klasse's bookTool Methode gesetzt
          workToolId -> parent id
          booked -> Ausgeliehen? Von der WorkTool Klasse's bookTool Methode gesetzt
          checkedOut -> Abgeholt? Kann nur bei booked = true von Admins auf true gesetzt werden
          returned -> Zurückgegeben? Kann nur bei booked = true & checkedOut = true von Admins auf true gesetzt werden
          start -> Start der Ausleihzeitspanne
          duration -> Dauer der Ausleihspanne
    -   Methoden
          getId
          getOwner
          getWorkToolId
          isBooked
          isCheckedOut
          isReturned
          getStart
          getDuration

          setOwner
          setBooked
          setCheckedOut
          setReturned
          setStart
          setDuration
          deleteWTInstance -> Löscht den Eintrag der Instanz in DB

          static

          getWTInstance -> Gibt WTInstance Objekt mit übergebener Id zurück
          newWTInstance -> Erstellt Eintrag in DB & gibt WTInstance Objekt zurück - Von der WorkTool Klasse's addMember Methode genutzt

# Langfristig

## Funktionen

1.  Löschen des eigenen Termins:
    Das Löschen des eigenen Termins noch vor dem Startzeitpunkt ist zu kurzfristig möglich. Minuten vor dem Termin kann niemand mehr darauf reagieren - Termin neuvergeben etc. Dies sollte schon viel früher blockiert werden. Wunsch wäre ein Werktag.

2.  Größe der Wochenansicht: ✓
    Wochenansicht und Tagesansicht sind sehr kleinteilig gehalten. Die Formatierung könnte größere Elemente haben. Moderne Monitore lassen mehr Spielraum als wir nutzen.

Größere Formatierung mit mehr Platz für die Slots wäre besser.

3.  Mailanhang:
    Momentan in Bezug auf erlaubte Dateitypen und Größe beschränkt. Gewünscht wären 500MB mit PDF, TIFF, DWG, DXF, STL und SAT Endungen möglich und automatischer Löschung nach 4 Tagen.  
