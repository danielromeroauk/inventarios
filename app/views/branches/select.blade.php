<table class="table table-stripped table-hover table-bordered">
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
<script>
    $('.auk-select').on('click', function(){
        $this = $(this);

        $('#branch').val($this.attr('auk-branch'));
        $('#branch_id').val($this.attr('id'));
    });
</script>