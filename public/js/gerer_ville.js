$(document).ready(uploadVille)

function Lieu(id, nom, codePostal) {
    this.id = id;
    this.nom = nom;
    this.codePostal = codePostal;
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
                    let lienModifier = "supprimer" + ville.id;
                    let lienSupprimer = "modifier" + ville.id;
                    let ligne = $(`<li><div id="ville ${ville.id}">${ville.nom} ${ville.codePostal} 
                                <a href="${lienModifier}">Modifier</a>
                                <a href="${lienSupprimer}">Supprimer</a></div></li>`);
                    $(`#list-ville`).append(ligne);
                }
            }
        )
        .fail()
        .always();
}

function modifierVille(){

}