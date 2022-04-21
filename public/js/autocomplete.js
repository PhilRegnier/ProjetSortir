/*
 Autocomplete inspiré largement de w3schools

 Pour appeler : autocomplete(document.getElementById("inputId"), autocompleteArray, flag);

    flag = 0 pour mettre en valeur n'importe quelle sous-chaine
    de caractère des éléments d'autocompleteArray

 */

function autocomplete(inp, arr, flag) {

    let currentFocus = -1;

    // execute a function when someone writes in the text field
    inp.addEventListener("input", function(e) {
        let a, b;

        let val = this.value;

        // close any already open lists of autocompleted values
        closeAllLists();

        if (!val) { return false;}

        // create a DIV element that will contain the items (values)
        a = document.createElement("div");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");

        // append the DIV element as a child of the autocomplete container
        this.parentNode.appendChild(a);

        if (flag === 1) {
            for (const ligne of arr) {
                if (ligne.substring(0, val.length).toUpperCase().indexOf(val.toUpperCase()) === 0) {
                    b = document.createElement("div");
                    b.innerHTML = "<strong>" + ligne.substring(0, val.length) + "</strong>";
                    b.innerHTML += ligne.substring(val.length);
                    b.innerHTML += "<input type='hidden' value='" + ligne + "'>";
                    b.addEventListener("click", function (e) {
                        inp.value = this.getElementsByTagName("input")[0].value;
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
        }
        else {
            for (const ligne of arr) {
                let index = ligne.toUpperCase().indexOf(val.toUpperCase())
                if (index >= 0) {
                    b = document.createElement("div");
                    b.innerHTML = ligne.substring(0, index);
                    b.innerHTML += "<strong>" + ligne.substring(index, index+val.length) + "</strong>";
                    b.innerHTML += ligne.substring(index+val.length);
                    b.innerHTML += "<input type='hidden' value='" + ligne + "'>";

                    b.addEventListener("click", function (e) {
                        inp.value = this.getElementsByTagName("input")[0].value;
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
        }
    });
    /*
      Execute a function presses a key on the keyboard
      If the arrow DOWN or UP key is pressed, increase the currentFocus variable,
      and make the current item more visible.
      If the ENTER key is pressed, prevent the form from being submitted,
      and simulate a click on the "active" item
     */
    inp.addEventListener("keydown", function(e) {
        let x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode === 40) {
            currentFocus++;
            addActive(x);
        } else if (e.keyCode === 38) {
            currentFocus--;
            addActive(x);
        } else if (e.keyCode === 13) {
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });
    /*
        Function to classify an item as "active"
        start by removing the "active" class on all items
        add class "autocomplete-active"
     */
    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        x[currentFocus].classList.add("autocomplete-active");
    }
    // a function to remove the "active" class from all autocomplete items
    function removeActive(x) {
        for (let i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }
    // close all autocomplete lists in the document, except the one passed as an argument:
    function closeAllLists(elmnt) {
        let x = document.getElementsByClassName("autocomplete-items");
        for (let i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    // Close list when someone clicks in the document:
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

