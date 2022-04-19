
$(document).ready(updateSortie);

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

let lieux = [];

function updateAffichageLieu(lieu) {
    $('#rue').text(lieu.rue);
    $('#latitude').text(lieu.latitude);
    $('#longitude').text(lieu.longitude);
}

function updateSortie() {

    let villeId = document.getElementById('ville').value;
    document.getElementById('lieu').innerHTML = "";
    document.getElementById('rue').innerHTML = "";
    document.getElementById('latitude').innerHTML = "";
    document.getElementById('longitude').innerHTML = "";

    $.ajax(
        {
            url: BASE_URL + '/villes/' + villeId,
            method: 'GET'
        }
    )
    .done(
        (ville) => {
            $('#codePostal').text(ville.codePostal);

            let selected = true;

            lieux = [];

            for (const lieu of ville.lieux) {

                lieux.push( new Lieu(lieu.id, lieu.nom, lieu.rue, lieu.latitude, lieu.longitude))

                let option = $(`<option value="${lieu.id}">${lieu.nom}</option>`);
                $('#lieu').append(option);
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
