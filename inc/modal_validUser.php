<!--
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Fenêtre modal pour la validation d'un utilisateur
-->
<div class="modal fade bs-example-modal-lg" id="myValidUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Vérification de profil</h4>
            </div>            
                <div class="modal-body">  
                    
                    <table id="tableValidUser" class="table">
                    <tr>
                        <td class="col-xs-2">Prénom</td>
                        <td class="col-xs-2"><span id="vu_inputFirstname"></span></td>
                        <td class="col-xs-4" rowspan="4"><img id="vu_imgProfil" class="img-responsive"  alt="Image de profil" height="200" width="200"></td>
                    </tr>
                    <tr>
                        <td>Nom</td>
                        <td><span id="vu_inputName"></span></td>
                    </tr>
                    <tr>
                        <td>E-mail</td>
                        <td><span id="vu_inputEmail"></span></td>
                    </tr>
                    <tr>
                        <td>Nom d'utilisateur</td>
                        <td><span id="vu_inputPseudo"></span></td>
                    </tr>                    
                    <tr>
                        <td>Canton</td>
                        <td><span id="vu_inputCity"></span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tarif</td>
                        <td><span id="vu_inputTarif"></span></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Diplômes</td>
                        <td id="vu_cellDiplomes"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Matières</td>
                        <td><span id="vu_inputMatiere"></span></td>
                        <td></td>
                      </tr>
                </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>  
                    <input type="button" id="submitValidUser" class="btn btn-primary" value="Valider"/>
                </div>            
        </div>
    </div>
</div>