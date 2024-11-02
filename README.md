# Progetto Tecnologie Web

## Badge delle GitHub Actions

| Nome         | Badge                                               |
|--------------|-----------------------------------------------------|
| Sync (main)     | [![sync](https://github.com/hash-Guglio/tecweb/actions/workflows/deploy.yml/badge.svg?branch=main)](https://github.com/hash-Guglio/tecweb/actions/workflows/deploy.yml)      |
| Broken Links         | [![broken links](https://github.com/hash-Guglio/tecweb/actions/workflows/broken-links.yml/badge.svg?branch=main)](https://github.com/hash-Guglio/tecweb/actions/workflows/broken-links.yml) |
| A11y audit           | [![A11y audit](https://github.com/hash-Guglio/tecweb/actions/workflows/a11y-audit.yml/badge.svg?branch=main)](https://github.com/hash-Guglio/tecweb/actions/workflows/a11y-audit.yml)  |
| HTML5 validator      | [![HTML5 validator](https://github.com/hash-Guglio/tecweb/actions/workflows/validate-html.yml/badge.svg?branch=main)](https://github.com/hash-Guglio/tecweb/actions/workflows/validate-html.yml)  |
| Site performance     | [![site performance](https://github.com/hash-Guglio/tecweb/actions/workflows/pagespeed-performance.yml/badge.svg?branch=main)](https://github.com/hash-Guglio/tecweb/actions/workflows/pagespeed-performance.yml) |

## Accesso alla VPS

Puoi accedere al sito web utilizzando il seguente link:

``` bash
https://[branch].tecweb.guglielmobarison.app
```

La pipeline di integrazione continua è gestita tramite GitHub Actions e include il deploy automatico dei branch (main, dev) sul server, insieme a una serie di controlli automatici.

## Utilizzo del container Docker
Per l'avvio tramite Docker è possibile utilizare il seguente comando all'interno della cartella ```_docker```
``` bash
docker-compose up
bash
```
#### Eseguire il container in background
```bash
docker-compose up -d
```
#### Eseguire il container in una qualsiasi altra cartella
```bash
docker-compose -f path/to/docker-compose.yml up
```
#### Forzare la creazione e la build del container
```bash
docker-compose up --build --force-recreate
```
