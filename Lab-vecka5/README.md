# Lab-Vecka5 ReadMe

## Reflektionsfrågor

### Varför ändrar vi aldrig filer direkt på kontrollnoden? Vad händer om någon gör det?
Gör man ändringar i kontrollnoden så kan den inte pusha upp ändringarna som gjorts till Github. Kontrollnoden är bara till för att kontrollera/testa filerna.

### Varför läggs secrets.yml i vagrant/-mappen istället för att committas till Git?
Secrets-mappen innehåller känslig data så som lösenord och användarnamn till t.ex. en DB eller Webserver. Om nån ska ladda ner repo:t 

### Vad är skillnaden mellan git pull och git fetch origin?
git fetch origin: Hämtar och tittar
git pull: hämtar och mergar i nuvarande branch

### Om din kollega pushar en ändring till main medan du arbetar på din branch — påverkar det din branch? Varför eller varför inte?

### Vad är skillnaden mellan kontrollnodens SSH-nyckel och Windows-datorns SSH-nyckel? Varför behöver båda vara tillagda på GitHub?
