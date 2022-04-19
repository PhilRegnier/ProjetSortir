
let codePostal = null;
let rues = [];

$(document).ready(updateLieu);

document.getElementById('ville').addEventListener('change', updateLieu);
document.getElementById('rue').addEventListener('keyup', updateLieuRue);
document.getElementById('rue').addEventListener('change', updateLieuGeo);

function updateLieu() {

    let villeId = document.getElementById('ville').value;

    $.ajax(
        {
            url: BASE_URL + '/villes/' + villeId,
            method: 'GET'
        }
    )
        .done(
            (ville) => {
                $('#codePostal').text(ville.codePostal);
                codePostal = ville.codePostal;
            }
        )
        .fail()
        .always();
}

function updateLieuRue() {

    let recherche = document.getElementById('rue').value;

    $.ajax(
        {
            url: 'https://api-adresse.data.gouv.fr/search/?q=recherche&postcode=' + codePostal + '&autocomplete=1',
            method: 'GET'
        }
    )
        .done(
            (adresse) => {
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