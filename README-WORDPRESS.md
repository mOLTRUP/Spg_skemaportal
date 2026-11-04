# ITD Spørgeskemaportal – WordPress-layouttest (v1.0)
Denne branch ("wordpress-baseline") er en WordPress-pluginpakke bygget på de fire **uændrede, godkendte GitHub-sider**:
- index.html – ITD-overblik
- virksomhed.html – Opret virksomhed
- udgiv.html – Udgiv spørgeskema
- skabeloneditor.html – Skabeloneditor (inkl. faste svarmetoder og springlogik)
- data/kundetilfredshed_v1.json – kundetilfredshedsskabelon

## Installation
1. Tag backup af WordPress og databasen. **Slet ikke hele WordPress-installationen eller brugerne**.
2. Deaktiver det tidligere test-plugin "ITD Skemaportal" (v0.2.x). Slet det ikke, medmindre databasen er sikkerhedskopieret.
3. Hent **ZIP for denne branch** fra GitHub ("Code" → "Download ZIP" eller via `https://github.com/mOLTRUP/Spg_skemaportal/archive/refs/heads/wordpress-baseline.zip`).
4. I WordPress: Plugins → Tilføj plugin → Upload plugin → installer ZIP og aktiver **ITD Skemaportal – Godkendt GitHub-layout**.
5. Log ind som WordPress-**administrator** og åbn `https://itd.relogic.dk/skema/`. Ingen kortkode nødvendig.
6. Test links og formularer. Visningen er standalone, så temaets header og footer vises ikke på /skema.

## Før SQL-integration
- Alle skabeloner, virksomheder og besvarelsestest i denne version gemmes **kun i browserens localStorage** præcis som på GitHub.
- PHP-pluginet opretter eller ændrer **ingen SQL-tabeller** og importerer ikke data fra det tidligere plugin.
- Undlad at indtaste personfølsomme eller rigtige kundedata i prototypen.
- Kun eksisterende WordPress-administratorer kan åbne /skema og undersider. ITD/virksomhed/respondent-roller tilføjes senere.
- Det gamle plugin kan forblive installeret men deaktiveret, så tidligere databasetabeller og testdata bevares.

## Test
Kontrollér /skema/, /skema/virksomhed.html, /skema/udgiv.html, /skema/skabeloneditor.html.
Vælg den gemte "Kundetilfredshedsundersøgelse – funktionstest", og kontrollér springregler.
SQL-integration bygges senere ovenpå disse låste layouts. 
