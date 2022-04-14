let BASE_URL = 'http://127.0.0.1:8000/api';

$(document).ready(updatePage);

document.getElementById('ville').addEventListener('change', updatePage);
document.getElementById('lieu').addEventListener('change', updateLieu);

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

function updatePage() {

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
