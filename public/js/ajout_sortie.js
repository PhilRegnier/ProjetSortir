
let currentVille = null;
let adresses = [];
let lieux = [];

$(document).ready(updateSortie);

document.getElementById('ville').addEventListener('change', updateSortie);
document.getElementById('lieu').addEventListener('change', updateLieu);
document.getElementById('lieurue').addEventListener('input', rechercheLieuAPI);
document.getElementById('lieurue').addEventListener('change', updateLieuAPI);

function Lieu(id, nom, rue, latitude, longitude) {
    this.id = id;
    this.nom = nom;
    this.rue = rue;
    this.latitude = latitude;
    this.longitude = longitude;
}

function Adresse(rue, latitude, longitude) {
    this.rue = rue;
    this.latitude = latitude;
    this.longitude = longitude;
}

function Ville(id, nom, codePostal) {
    this.id = id;
    this.nom = nom;
    this.codePostal = codePostal;
}

function updateAffichageSortie(lieu) {
    $('#rue').text(lieu.rue);
    if (lieu.latitude) {
        $('#latitude').text(lieu.latitude);
    } else {
        $('#latitude').text('--');
    }
    if (lieu.longitude) {
        $('#longitude').text(lieu.longitude);
    } else {
        $('#longitude').text('--');
    }
}

function resetLieu() {
    document.getElementById('rue').innerHTML = "";
    document.getElementById('latitude').innerHTML = "";
    document.getElementById('longitude').innerHTML = "";
}

function updateSortie() {

    let villeId = document.getElementById('ville').value;
    document.getElementById('lieu').innerHTML = "";
    resetLieu();

    $.ajax(
        {
            url: BASE_URL + '/villes/' + villeId,
            method: 'GET'
        }
    )
    .done(
        (ville) => {
            currentVille = new Ville(ville.id, ville.nom, ville.codePostal);
            $('#codePostal').text(ville.codePostal);

            let selected = true;

            lieux = [];

            for (const lieu of ville.lieux) {

                lieux.push(new Lieu(lieu.id, lieu.nom, lieu.rue, lieu.latitude, lieu.longitude))

                $('#lieu').append($(`<option value="${lieu.id}">${lieu.nom}</option>`));
                if (selected) {
                    updateAffichageSortie(lieu);
                    selected = false;
                }
            }
        }
    )
    .fail()
    .always();
}

function updateLieu() {

    let lieuId = document.getElementById('lieu').value;
    for (const lieu of lieux) {
        if (lieu.id == lieuId) {
            updateAffichageSortie(lieu);
        }
    }
}

function rechercheLieuAPI() {

    let recherche = document.getElementById('lieurue').value;
    if (!recherche) return;

    $.ajax(
        {
            url: 'https://api-adresse.data.gouv.fr/search/?q=' + recherche + '&postcode=' + currentVille.codePostal + '&limit=5&autocomplete=1',
            method: 'GET'
        }
    )
        .done(
            (donnees) => {
                adresses.length = 0;
                let autocompleteArray = [];
                for (const adresse of donnees.features) {
                    adresses.push(new Adresse(adresse.properties.name, adresse.properties.y, adresse.properties.x));
                    autocompleteArray.push(adresse.properties.name);
                }
                autocomplete(document.getElementById("lieurue"), autocompleteArray, 0);
            }
        )
        .fail()
        .always();
}

function updateLieuAPI() {

    let rue = document.getElementById('lieurue').value;
    for (const adresse of adresses) {
        if (adresse.rue == rue) {
            document.getElementById('lieulatitude2').innerHTML = "";
            document.getElementById('lieulongitude2').innerHTML = "";
            if (adresse.latitude && adresse.longitude) {
                $('#lieulatitude').val(adresse.latitude);
                $('#lieulongitude').val(adresse.longitude);
            } else {
                $('#lieulatitude').val(0);
                $('#lieulongitude').val(0);
            }
            break;
        }
    }
}

function open_mod() {
    $('#lieutitre').text("Créer un nouveau lieu à " + currentVille.nom);
    $('#lieunom').val("");
    $('#lieurue').val("");
    $('#lieulatitude').val("");
    $('#lieulongitude').val("");
    document.getElementById('modal').className = 'open';
}

function close_mod() {
    document.getElementById('modal').className = 'close';
}

function valideLieu() {

    const regex = new RegExp('/^[0-9]+.[0-9]+$');

    let nom = document.getElementById('lieunom').value;
    let rue = document.getElementById('lieurue').value;
    let latitude = document.getElementById('lieulatitude').value
    let longitude = document.getElementById('lieulongitude').value

    // TODO : controle de la valeur de longitude et latitude (revoir le regex)
    //  en attendant de les récupérer de l'API Geo

    if (!nom) return false;
    if (!rue) return false;

    if (!latitude || !regex.test(latitude)) {
        latitude = 0;
    }
    if (!longitude || !regex.test(longitude)) {
        longitude = 0;
    }
    let nouveauLieu = new Lieu(
        -1,
        nom,
        rue,
        latitude,
        longitude
    );

    enregistreNouveauLieu(nouveauLieu);
}

function enregistreNouveauLieu(nouveauLieu) {
    $.ajax(
        {
            type: 'POST',
            url: BASE_URL + '/lieux',
            contentType: "application/json; charset=utf-8",
            datatype: 'json',
            data: JSON.stringify({
                "nom": nouveauLieu.nom,
                "rue": nouveauLieu.rue,
                "latitude": nouveauLieu.latitude,
                "longitude": nouveauLieu.longitude,
                "ville": "/api/villes/" + currentVille.id
            }),
        }
    )
        .done(
            (lieu) => {
                nouveauLieu.id = lieu.id;
                nouveauLieu.nom = lieu.nom;
                nouveauLieu.rue = lieu.rue;
                nouveauLieu.latitude = lieu.latitude;
                nouveauLieu.longitude = lieu.longitude;
                lieux.push(nouveauLieu);
                $('#lieu').append($(`<option value="${lieu.id}" selected>${lieu.nom}</option>`));
                updateAffichageSortie(lieu);
            }
        )
        .fail()
        .always();
}
