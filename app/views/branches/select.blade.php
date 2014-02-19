<table class="table table-striped table-hover table-bordered">
    <thead>
        <th>Código</th>
        <th>Nombre</th>
    </thead>
    <tbody>
        @foreach($branches as $branch)
            <tr>
                <td>
                    {{ '<button id="'
                        . $branch->id
                        . '" class="btn btn-success auk-select" data-dismiss="modal" auk-branch="'
                        . $branch->name
                        .'"> <span class="glyphicon glyphicon-hand-right"></span> '
                        . $branch->id
                        . '</button>' }}
                </td>
                <td>
                    {{ $branch->name }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@if(isset($_GET['campo1'], $_GET['campo2']))
    <script>
        $('.auk-select').on('click', function(){
            $this = $(this);

            $('#'+"{{ $_GET['campo1'] }}").val($this.attr('auk-branch'));
            $('#'+"{{ $_GET['campo2'] }}").val($this.attr('id'));
        });
    </script>
@endif