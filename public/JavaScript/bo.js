var bo = (function()
{
    var bo={};
    bo.Tchat = function(question)
    {
        this.question = question || "";

    }

    bo.Tchat.prototype.toutEstSaisie = function() {
        return this.question!=="";
    }

    bo.Tchat.prototype.informations = function() {
        return `User :  ${this.question}`;
    }

    return bo;
})();