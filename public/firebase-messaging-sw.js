// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts("https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js");
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
    apiKey: 'AIzaSyDTnudDUlLG0zwADT6GL7_FR4ZeBiQNeuw',
    authDomain: 'usi-project.firebaseapp.com',
    databaseURL: 'https://usi-project.firebaseio.com',
    projectId: 'usi-project',
    storageBucket: 'usi-project.appspot.com',
    messagingSenderId: '1005984774841',
    appId: '1:1005984774841:web:3c006a224bd1d87109f968',
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
    console.log("Message received.", payload);
    const title = "Hello world is awesome";
    const options = {
        body: "Your notificaiton message .",
        icon: "/firebase-logo.png",
    };
    return self.registration.showNotification(
        title,
        options,
    );
});
