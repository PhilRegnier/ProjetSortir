// $(document).ready(uploadVille)

document.getElementById('modifier').addEventListener('click', updateVille);

function Lieu(id, nom, codePostal) {
    this.id = id;
    this.nom = nom;
    this.codePostal = codePostal;
}

let tabVilles = [];

function open_mod(){

    let modal = document.getElementById('modal');
    modal.className += 'open';

    $.ajax(
        {
            url: BASE_URL + "/villes.json",
            method: "GET"
        }
    )
        .done(
            (villes) => {
                for (const ville of villes) {

                    tabVilles.push( new Lieu(ville.id, ville.nom, ville.codePostal))

                }
            }
        )
        .fail()
        .always();
}

function uploadVille(){

    $.ajax(
        {
            url: BASE_URL + "/villes.json",
            method: "GET"
        }
    )
        .done(
            (villes) => {
                for (const ville of villes){
                    let lienModifier = "modifier" + ville.id;
                    let lienSupprimer = "supprimer" + ville.id;
                    let ligne = $(`<li>
                                        <div id="villez" value="7">${ville.nom}</div>
                                        <div id="codePostal" value="7">${ville.codePostal}</div>
                                        <div>
                                            <a href="${lienModifier}" onclick="function open_mod(){var modal = document.getElementById('modal'); modal.className += 'open';} open_mod(); return false;">Modifier</a>
                                            <a href="${lienSupprimer}">Supprimer</a>
                                        </div>
                                    </li>`);
                    $(`#list-ville`).append(ligne);
                }
            }
        )
        .fail()
        .always();
}

function updateVille(){

    let villeId = document.getElementById('villez').value;
    document.getElementById("villez").innerHTML = "<input id='edit'>";

    $.ajax(
        {
            url: BASE_URL + '/villes/' + villeId,
            method: 'GET'
        }
    )
        .done(
            (ville) => {

            }
        )
        .fail()
        .always();
}