Stud.IP WorkplaceAllocation Plugin
=
Das Plug-in erlaubt es Einrichtungen die Buchungen ihrer studentischen Arbeitsplätze im Stud.IP System zu organisieren.

Nach Aktivierung des Plug-ins auf der Seite der Einrichtung können im Menüpunkt "Verwaltung" Arbeitsplätze erstellt und konfiguriert werden.

Die Anmeldung für Zeitslots auf Arbeitsplätzen erfolgt über den Menüpunkt "Arbeitsplätze" auf der Seite der Einrichtung.

Installation
-
Um das Plug-in zu installieren muss erst eine installierbare ZIP Datei gebaut werden.
Diese Dev Version benutzt das Build-Management Tool Make. Mit dem Befehl `make build` wird automatisch eine Installationsdatei aus dem aktuellen Stand erzeugt. Das erzeugte ZIP Archiv kann dann im Stud.IP Backend installiert werden.

Features
-
Erstellung von Arbeitsplätzen mit umfassenden Optionen für Zeitslots. Einstellungen für Tage, Öffnungszeiten, Pausen, Slotlänge, Limitierung des Anmeldezeitraums, Besondere Optionen wie "Ein Termin pro Tag und Nutzer" oder "Termine nur für Mitglieder der Einrichtung"

Zeitslots mit Link auf Profilseite des Nutzers, variabler Länge, Kommentarfunktion und nachträglicher Modifikationsmöglichkeit. 

Blockingliste zur temporären Sperrung von Nutzern von den Arbeitsplätzen. Standardzeitspanne von 7 Tagen. Individuell modifizierbar.

Versendung von Stud.IP Nachrichten mit modifizierbaren Texten bei Erstellung, Modifizierung und Löschung von Zeitslots sowie Veränderungen der Blockingliste an Betroffene. 

Mailingliste zur Benachrichtigung einer frei zusammenstellbaren Gruppe von Nutzern bei Erstellung, Modifizierung und Löschung von Zeitslots.  

Druckfunktion für Öffnungszeiten aller Arbeitsplätze im PDF Format

Version History:
-
v0.1
Initial
v0.2
Warteliste für belegte Terminblöcke hinzugefügt
v0.3
Warteliste für belegte Terminblöcke auf Nutzerwunsch deaktiviert
v0.4
Bugfix für Fehler bei neu erstellten Arbeitsplätzen ohne Tage
v0.5
Name bei Terminen ist Link auf Profil  
v0.6
Mailingliste für Terminänderungen hinzugefügt
v0.7
Terminansicht standartmäßig per Woche
Option der Beschränkung von Arbeitsplätzen für Einrichtungsmitglieder
Überarbeitung der Terminansicht
Darstellung von Kommentaren in Termin- und Druckansicht
Implementation eines locks für Modifikationen von Termindaten
v1.0
Verbesserung der Suchfunktion für das Hinzufügen zur Mailingliste
Implementation des Stud.IP CSRF Schutzes
Implementation des Stud.IP Coding-Stils