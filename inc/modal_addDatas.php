<!--
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Fenêtre modal pour ajouter du contenu additionnel
-->
<div class="modal fade bs-example-modal-lg" id="myAddDatas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myAddDatasLabel">Ajouter du contenu</h4>
            </div>
            <form method="post" enctype="multipart/form-data" id="addDatasForm" name="addDatasForm" class="form-horizontal">  
                <div class="modal-body">
                    <div id="alertPopUpAddDatas" role="alert"></div>   

                    <div class="form-group">
                        <label for="ad_inputType" class="col-sm-2 control-label">Type de données</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="ad_inputType" name="ad_inputType" required>
                                <option value="resume">Résumé</option>
                                <option value="exercice">Exercice</option>
                                <option value="question">Question</option>
                                <option value="correction">Correction</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ad_inputKeyword" class="col-sm-2 control-label">Mots-clés <span id="keyPopOver" class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="popoverKey" title="Informations" data-trigger="hover" data-content="Veuillez séparer les mots clés par des <kbd>;</kbd>"></span></label>
                        <div class="col-sm-8">
                            <textarea id="ad_inputKeyword" name="ad_inputKeyword" class="form-control" style="resize: none" rows="3" required></textarea>
                        </div>
                    </div>       

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Accès</label>
                        <div class="col-sm-8">
                            <input type="radio" name="ad_Access" value="public" checked>Publique
                            <input type="radio" name="ad_Access" value="private">Privé
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ad_inputMatiere" class="col-sm-2 control-label">Matière</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="ad_inputMatiere" name="ad_inputMatiere" required>
                                <!-- Formulaire rempli à l'aide de JQuery -->
                            </select>
                        </div>
                    </div>
                    <input type="text" id="ad_inputUser" name="ad_inputUser" value="<?php echo $_SESSION['_id'];?>" hidden>
                    
                    <div class="form-group" >
                        <label for="ad_inputDatas" class="col-sm-2 control-label">Fichier</label>
                        <div class="col-sm-8">
                            <input type="file" accept='.pdf' id="ad_inputDatas" name="ad_inputDatas" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <input type="submit" id="submitAddDatas" class="btn btn-primary" value="Ajouter contenu"/>
                </div>
            </form>
        </div>
    </div>
</div>