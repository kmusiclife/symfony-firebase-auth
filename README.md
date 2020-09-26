# Symfony and Firebase Authentication

Firebase authentication and firestore, a symfony project to build an authentication service with symfony, using the Symfony Security bundle and a local Role integration.

To get started, just clone the this repository and run below commands:

    git clone https://github.com/kmusiclife/symfony-firebase-auth.git
    cd symfony-firebase-auth
    composer install
    cp .env.dist .env
    cp public/js/firebase-config.js.dist public/js/firebase-config.js

1. Set up your firebase authentication(email method) and firestore service
2. Edit and add Firebase SDK snippet(Web App) to public/js/firebase-config.js
3. Download your-projectname-firebase-adminsdk-xxxxx.json from Firebase Admin SDK in Firebase's Service Account and copy credentials.json to your project dir
4. Edit your .env for using database because of authentication for symfony roles etc.

after that, easy to start your server like below command

    bin/console doctrine:database:create
    bin/console doctrine:schema:update --force
    symfony server:start

We provide profile service using firestore. You can try to access /profile