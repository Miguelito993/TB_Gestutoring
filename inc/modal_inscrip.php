<div class="modal fade bs-example-modal-lg" id="myInscription" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myInscriptionLabel">Inscription</h4>
            </div>
            <form method="post" enctype="multipart/form-data" id="inscripForm" class="form-horizontal">  
                <div class="modal-body">
                    <div id="alertPopUpInscrip" role="alert">
                    </div>   

                    <div class="form-group">
                        <label for="inputFirstname" class="col-sm-2 control-label">Prénom</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputFirstname" name="inputFirstname" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputName" class="col-sm-2 control-label">Nom</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputName" name="inputName" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail" class="col-sm-2 control-label">E-mail</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="inputEmail" name="inputEmail" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="inputPseudo" class="col-sm-2 control-label">Pseudo</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputPseudo" name="inputPseudo" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputPassword" class="col-sm-2 control-label">Mot de passe</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="inputPassword" name="inputPassword" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputPassword2" class="col-sm-2 control-label">Confirmer mot de passe</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="inputPassword2" name="inputPassword2" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputCity" class="col-sm-2 control-label">Canton</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="inputCity" name="inputCity" required>
                                <!-- Formulaire rempli à l'aide de JQuery -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputType" class="col-sm-2 control-label">Type de compte</label>
                        <label class="radio-inline">
                            <input type="radio" name="inputType" value="Student" checked> Étudiant
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="inputType" value="Coach"> Répétiteur
                        </label>
                    </div>

                    <!-- Champ facultatif selon le type de compte -->

                    <div id="divEmailParent" class="form-group">
                        <label for="inputEmailParent" class="col-sm-2 control-label">E-mail d'un parent</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="inputEmailParent" name="inputEmailParent" required>
                        </div>
                    </div>

                    <div id="divTarif" class="form-group" hidden>
                        <label for="inputTarif" class="col-sm-2 control-label">Tarif</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="inputTarif" name="inputTarif" step="1" min="1" max="50">
                        </div>
                        <label class="control-label">CHF/Heure</label>
                    </div>

                    <div id="divMatiere" class="form-group" hidden>
                        <label for="inputMatiere" class="col-sm-2 control-label">Matières d'enseignements <span id="infoPopOver" class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="popover" title="Informations" data-trigger="hover" data-html="true" data-content="Veuillez utiliser la touche <kbd>Ctrl</kbd> pour sélectionner plusieurs matières"></span></label>
                        <div class="col-sm-8">
                            <select multiple class="form-control" id="inputMatiere" name="inputMatiere">
                                <!-- Formulaire rempli à l'aide de JQuery -->
                            </select>
                        </div>                    
                    </div>

                    <div id="divDiplome" class="form-group" hidden>
                        <label for="inputDiplome" class="col-sm-2 control-label">Diplômes</label>
                        <div class="col-sm-8">
                            <input type="file" accept='.pdf' id="inputDiplome" name="inputDiplome" multiple>
                        </div>
                    </div>

                    <!-- end Champ facultatif -->  
                    <div class="form-group">
                        <label for="inputImgProfile" class="col-sm-2 control-label">Image de profil</label>
                        <div class="col-sm-8">
                            <input type="file" id="inputImgProfile" name="inputImgProfile" required>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <input type="submit" id="submitInscription" class="btn btn-primary" value="Inscription"/>
                </div>
            </form>
        </div>
    </div>
</div>