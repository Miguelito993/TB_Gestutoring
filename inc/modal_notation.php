<!--
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Fenêtre modal pour noter un répétiteur
-->
<div class="modal fade bs-example-modal-lg" id="myNotation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myEventLabel">Notation</h4>
            </div>
            <form method="post" id="notationForm" class="form-horizontal">  
                <div class="modal-body">
                    <div id="alertPopUpNotation" role="alert"></div>   

                    <div class="form-group">
                        <label for="inputNote" class="col-sm-2 control-label">Note</label>                        
                        <div class="col-sm-8 rating">                
                            <a href="#10" class="starLink" title="Donner 10 étoiles">★</a>
                            <a href="#9" class="starLink" title="Donner 9 étoiles">★</a>
                            <a href="#8" class="starLink" title="Donner 8 étoiles">★</a>
                            <a href="#7" class="starLink" title="Donner 7 étoiles">★</a>
                            <a href="#6" class="starLink" title="Donner 6 étoile">★</a>
                            <a href="#5" class="starLink" title="Donner 5 étoiles">★</a>
                            <a href="#4" class="starLink" title="Donner 4 étoiles">★</a>
                            <a href="#3" class="starLink" title="Donner 3 étoiles">★</a>
                            <a href="#2" class="starLink" title="Donner 2 étoiles">★</a>
                            <a href="#1" class="starLink" title="Donner 1 étoile">★</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputComment" class="col-sm-2 control-label">Commentaire</label>
                        <div class="col-sm-8">
                            <textarea id="inputComment" class="form-control" style="resize: none" rows="3"></textarea>
                        </div>
                    </div>                    
                    <input type="text" id="idUser" hidden/>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <input type="submit" id="submitNotation" class="btn btn-primary" value="Noter"/>
                </div>
            </form>
        </div>
    </div>
</div>