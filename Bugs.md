## Fehler
1. Termine der Studierenden werden manchmal nicht angelegt:
Termine der Studierenden werden manchmal trotz Versandt einer Bestätigungsmail nicht angelegt. Dies kommt in der Zeit der Vollbelastung während der Abgabephase so zwischen 3 und 5 mal vor. 

Da es nur bei starker Nutzung vereinzelt auftritt könnte es mit Kollisionen zu tun haben.

Implementation eines locks für die Modifikation von Termindaten als Lösungsansatz. Es kann nur noch ein User gleichzeitig Termine an einem Arbeitsplatz modifizieren. Wirkung ist allerdings schwer prüfbar.

2. Alle Arbeitsplätze drucken funktioniert nicht:
Der Button produziert seit einiger Zeit nur eine Fehlermeldung.

Sie meinten das könnte seit dem Bug mit dem neu erstellten Arbeitsplatz ohne aktivierten Tagen so sein. Sind sich aber nicht sicher.