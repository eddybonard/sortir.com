var service = (
    function(bo)
    {
        var service={};
        var questionInutiles = [];

        service.ajouterUnequestion = function(question)
        {
            var questionAaJOUTER = new bo.Tchat(question);
            if(questionAaJOUTER.toutEstSaisie()) {
                questionInutiles.push(questionAaJOUTER);
                return true;
            }
            return false;
        }



        service.getQuestionInutiles = function()
        {
            return questionInutiles;
        }

        service.getQuestionDefault = function()
        {
            return new bo.Tchat("");
        }

        return service;
    }
)(bo);