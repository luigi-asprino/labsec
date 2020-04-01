# Docker container security lab

Questo repository mette a disposizione un docker compose per svolgere le esercitazioni del [corso di Sicurezza](https://www.cs.unibo.it/~babaoglu/courses/security/index.html).

**Nota** Il docker container del security lab è uno strumento in versione alfa (da considerarsi quindi come uno strumento instabile e da testare). Si consiglia pertanto l'uso della macchina virtuale disponibile al seguente [indirizzo](https://w3id.org/people/lgu/securityLab/index.html). 

### Prerequisiti

Il docker compose richiede:

* docker 
* docker-compose

### Building and Running

Da terminale su un sistema unix eseguire i comandi:

```
git clone https://github.com/luigi-asprino/labsec.git
cd labsec/docker-sec/
docker-compose build
docker-compose up
```

A questo punto le esercitazioni sono disponibili agli indirizzi condivisi sulle slide (e.g. [http://localhost/esercitazione1](http://localhost/esercitazione1)). 


