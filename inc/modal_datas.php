<div class="modal fade bs-example-modal-lg" id="myDatas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="myDataLabel">Banque de données  <small id="matiereText"></small></h4>
            </div>            
                <div class="modal-body">                                        
                    <div id="dataPublic" class="table-responsive" hidden>
                        <h4 id="titlePublic">Données publiques:</h4>
                        <table id="tablePublic" class="table">
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Lien</th>
                                <th></th>
                            </tr>
                        </table>
                    </div>                    
                    <div id="dataPrivate" class="table-responsive" hidden>
                        <h4 id="titlePrivate">Données privées:</h4>
                        <table id="tablePrivate" class="table">
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Lien</th>
                                <th></th>
                            </tr>
                        </table>                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>                    
                </div>            
        </div>
    </div>
</div>