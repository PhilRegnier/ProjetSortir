
$(document).ready(updateLieu);

document.getElementById('ville').addEventListener('change', updateLieu);

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
            }
        )
        .fail()
        .always();
}

