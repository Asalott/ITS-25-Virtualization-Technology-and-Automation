## Lab-Vecka4
# Del 6: Reflektionsfrågor

## Dessa frågor är avsedda att befästa förståelsen av säkerhetsmässiga och praktiska konsekvenser.

### Varför vägrar Ansible läsa ansible.cfg om mappen är world-writable? Vad är den säkerhetsrisk som detta skyddar mot?
För att förhindra att någon utomstående kan injecta skadlig kod i ansible (t.ex. instruktioner att ladda ner skadlig mjukvara). Endast Ansible bör ha tillgång till filen/mappen. 

### I produktionsmiljöer med hundratals servrar — hur hanterar man SSH-nycklar på ett säkert och skalbart sätt? Vad är problemet med att använda en enda nycklel?
Om nyckeln läcker så har man full access till alla servrar. Saknas spårbarhet (kan inte se vem som gjorde vad). Man kan inte återkalla access om någon slutar, då måste man byta nyckel på alla servrar. Dålig least privilege då alla får samma åtkomstnivå. 

### Varför vore ett Git-repo ett bättre alternativ för Ansible-konfigurationer i ett riktigt projekt? Vad ger Git som en delad mapp inte ger?
Spårbarhet. Kontroll över vem som kan göra ändrigar i vad. Då det ligger på internet så är det lättåtkommet och finns även möjlighet till att backa vid felkonfigurationer. Du behöver inte skriva ner ansiblefilerna manuellt. (Vi tror: att ansible inte gillar delade mappar / mappar alla har tillgång till).

### ansible ping returnerade 'changed: false'. Vad hade det betytt om det returnerat 'changed: true'?
Det betyder att kommandot har ändrat något i målmaskinen. I detta fall är det en ping och då ska ingenting ändras - så det är förväntat.

### Hur kopplar SSH-nyckelbaserad autentisering till principen om minsta privilegium från vecka 3?
Du behöver SSH-nyckel för att komma in på systemet (gärna med lösenord också). Detta gör så att inte vem som helst kan komma in på systemet. Om du har fler SSH-nycklar så kan man även spåra vem som gjort vad.
*OBS! Detta har vi inte gjort i labben
