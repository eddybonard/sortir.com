function publierSortie()
{
   window.confirm('Etes-vous sûr de vouloir publier la sortie ? ');
}

function supprimerProfil()
{
    window.confirm('Etes-vous sur de vouloir supprimer votre profil ?')
}

function modifierProfil()
{
    window.confirm('Etes-vous sur de vouloir modifier votre profil ?')
}

function annulerSortie()
{
    window.confirm('Etes-vous sur de vouloir annuler cette sortie ?')
}


function supprimerVille() {


    window.confirm('Etes-vous sur de vouloir supprimer cette ville ?')

}

function supprimerCampus() {


    window.confirm('Etes-vous sur de vouloir supprimer ce campus ?')

}

function suprimmerSortie() {


    window.confirm('Etes-vous sur de vouloir supprimer la sortie ?')

}

function formulaireLieu(formulairelieu) {
    document.getElementById(formulairelieu).style.display = "block"
}

function activerParticipant() {

    window.confirm('Etes-vous sur de vouloir activer ce participant ?')

}

function desactiverParticipant() {


    window.confirm('Etes-vous sur de vouloir désactiver ce participant ?')

}

function suprimmerParticipant() {


    window.confirm('Etes-vous sur de vouloir suprimmer ce participant ?')

}


   var boutonInscription = document.getElementById("inscription");
   var boutonDesincription = document.getElementById("desincriptions");
       boutonInscription.addEventListener("click", ()=>{
            boutonDesincription.style.display = "block";
            boutonInscription.style.display ="none";
           console.log("function display")
    });




