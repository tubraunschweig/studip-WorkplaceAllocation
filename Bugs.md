## Fehler
1. Termine der Studierenden werden manchmal nicht angelegt:
Termine der Studierenden werden manchmal trotz Versandt einer Bestätigungsmail nicht angelegt. Dies kommt in der Zeit der Vollbelastung während der Abgabephase so zwischen 3 und 5 mal vor.

Da es nur bei starker Nutzung vereinzelt auftritt könnte es mit Kollisionen zu tun haben.

Implementation eines locks für die Modifikation von Termindaten als Lösungsansatz. Es kann nur noch ein User gleichzeitig Termine an einem Arbeitsplatz modifizieren. Wirkung ist allerdings schwer prüfbar.

2. Werden neue Arbeitsplätze vor der Konfiguration sofort wieder gelöscht gibt es eine Fehlermeldung, das Löschen funktioniert aber trotzdem.

Vor der Konfiguration besitzen Arbeitsplätze kein Regelfeld. Beim Löschen eines Arbeitsplatzes wird automatisch dessen Regelfeld mitgelöscht. Es wird also versucht ein nicht-existentes Objekt zu löschen. 
