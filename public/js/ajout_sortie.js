
$(document).ready(updateSortie);

$("#lieurue").autocomplete({
    source: arrayGeo
})

document.getElementById('ville').addEventListener('change', updateSortie);
document.getElementById('lieu').addEventListener('change', updateLieu);
document.getElementById('rue').addEventListener('keyup', updateLieuRue);
document.getElementById('rue').addEventListener('change', updateLieuGeo);

function Lieu(id, nom, rue, latitude, longitude) {
    this.id = id;
    this.nom = nom;
    this.rue = rue;
    this.latitude = latitude;
    this.longitude = longitude;
}

function Ville(id, nom, codePostal) {
    this.id = id;
    this.nom = nom;
    this.codePostal = codePostal;
}

let currentVille = null;

let lieux = [];

function updateAffichageLieu(lieu) {
    $('#rue').text(lieu.rue);
    $('#latitude').text(lieu.latitude);
    $('#longitude').text(lieu.longitude);
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
                    updateAffichageLieu(lieu);
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
            updateAffichageLieu(lieu);
        }
    }
}

function updateLieuRue() {

    let recherche = document.getElementById('rue').value;
    let codePostal = document.getElementById('codePostal').value;
    console.log(recherche);
    console.log(codePostal);

    $.ajax(
        {
            url: 'https://api-adresse.data.gouv.fr/search/?q=' + recherche + '&postcode=' + codePostal + '&autocomplete=1&limit=15',
            method: 'GET'
        }
    )
        .done(
            (adresses) => {
                $('#rue').text(adresse.name);
            }
        )
        .fail()
        .always();
}

function updateLieuGeo() {

    let recherche = document.getElementById('rue').value;

    $.ajax(
        {
            url: 'https://api-adresse.data.gouv.fr/search/?q=recherche&postcode=' + codePostal + '&autocomplete=1',
            method: 'GET'
        }
    )
        .done(
            (adresse) => {
                $('#latitude').text(adresse.y);
                $('#longitude').text(adresse.x);
            }
        )
        .fail()
        .always();
}

function open_mod() {
    let modal = document.getElementById('modal');
    modal.className = 'open';
}

function close_mod() {
    let modal = document.getElementById('modal');
    modal.className = 'close';
}

function valideLieu() {

    const regex = new RegExp('/^[0-9]+.[0-9]+$');

    let nom = document.getElementById('lieunom').value;
    let rue = document.getElementById('lieurue').value;
    let latitude = document.getElementById('lieulatitude').value
    let longitude = document.getElementById('lieulongitude').value

    // TODO : controle de la valeur de longitude et latitude (revoir le regex)
    //  en attendant de les récupérer de l'API Geo

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
                updateAffichageLieu(lieu);
            }
        )
        .fail()
        .always();
}
