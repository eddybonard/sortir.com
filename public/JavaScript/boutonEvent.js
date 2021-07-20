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

function supprimerVille() {


    window.confirm('Etes-vous sur de vouloir supprimer cette ville ?')

}

function supprimerCampus() {


    window.confirm('Etes-vous sur de vouloir supprimer ce campus ?')

}

function suprimmerSortie() {


    window.confirm('Etes-vous sur de vouloir supprimer la sortie ?')

}

function boutonInsciption() {

    document.getElementById("inscription").addEventListener("click", function(){
        document.getElementById("desincriptions").hidden=false;
        document.getElementById("inscription").hidden=true;
    }, false);
    console.log("hello")

}

