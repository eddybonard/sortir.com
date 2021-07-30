var service = (
    function(bo)
    {
        var service={};
        var clequestion="questionTchat";
        var savoirsInutiles;

        (function()
        {
            savoirsInutiles = JSON.parse(localStorage.getItem(clequestion))||[];
            savoirsInutiles.forEach(value => {Object.setPrototypeOf(value,bo.Tchat.prototype);});
            savoirsInutiles.forEach(value => {console.log(value)});

        })();

        service.ajouterSavoir = function(question)
        {
            var savoirAAjouter = new bo.Tchat(question);
            if(savoirAAjouter.toutEstSaisie()) {
                savoirsInutiles.push(savoirAAjouter);
                service.persister();
                return true;
            }
            return false;
        }




        service.getSavoirsInutiles = function()
        {
            return savoirsInutiles;
        }

        service.getSavoirDefaut = function()
        {
            return new bo.Tchat(" ");
        }

        service.persister = function()
        {
            localStorage.setItem(clequestion,JSON.stringify(savoirsInutiles));
        }


        return service;
    }
)(bo);