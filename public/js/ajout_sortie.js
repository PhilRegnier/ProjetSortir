let BASE_URL = 'http://127.0.0.1:8000';

$(document).ready(updatePage);

document.getElementById('ville').addEventListener('change', updatePage);

function updatePage() {

    let villeId = document.getElementById('ville').value;

    document.getElementById('codePostal').value =

        $.ajax(
            {
                url: BASE_URL + '/ville/obtenir/' + villeId,
                method: 'GET'
            }
        )
        .done(
            (donnees) => {
                $('#codePostal').text(donnees.ville.codePostal);

            }
        )
        .fail()
        .always();
}
