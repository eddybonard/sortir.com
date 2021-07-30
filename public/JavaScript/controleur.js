var controleur =(
    function(service)
    {
        var controleur={};
        controleur.initialiserFormulaire = function()
        {
            var question = service.getSavoirDefaut();
            document.getElementById("question").value=question.question;
            controleur.afficherSavoirs();
        }


        controleur.afficherSavoirs = function()
        {

            var olSavoir = document.getElementById("zoneDeTchat");

            service.getSavoirsInutiles().forEach((value, index, array)=>
                {

                    var pQuestion = document.createElement("p");
                    pQuestion.className = "question";

                    pQuestion.innerText = value.informations();
                    console.log(value);

                    olSavoir.appendChild(pQuestion);
                }
            )
        }

        controleur.ajouter = function() {
            var ajoutOk = service.ajouterSavoir(document.getElementById("question").value)

            if (ajoutOk) {
                controleur.afficherSavoirs();
                controleur.initialiserFormulaire();
            }
            else {
                alert("Tous les champs sont obligatoires");
            }
        }


        return controleur;
    }
)(service);


//Initialisation des traitements
controleur.initialiserFormulaire();