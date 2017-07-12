<!--
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Fenêtre modal pour créer un événement
-->
<div class="modal fade bs-example-modal-sm" id="myEvent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myEventLabel">Événement</h4>
            </div>
            <form method="post" id="eventForm" class="form-horizontal">  
                <div class="modal-body">
                    <div id="alertPopUpEvent" role="alert"></div>   

                    <div class="form-group">
                        <label for="inputTitle" class="col-sm-2 control-label">Titre</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputTitle" value="Libre" required disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputDate" class="col-sm-2 control-label">Date</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="inputDate" required disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputHeure" class="col-sm-2 control-label">Heure</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="inputHeure"  required>
                                <!-- Formulaire rempli à l'aide de JQuery -->
                            </select>
                        </div>
                    </div>
                    <input type="text" id="idCoach" hidden />

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <input type="submit" id="submitEvent" class="btn btn-primary" value="Créer événement"/>
                </div>
            </form>
        </div>
    </div>
</div>