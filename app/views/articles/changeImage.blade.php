 <h2>{{ $article->name }}</h2>

 {{ Form::open(array('url' => 'articles/image', 'files' => true)) }}

    <div class="input-group">
        {{ Form::file('image', array('class' => 'form-control', 'required')) }}
        <span class="input-group-btn">
            {{ Form::submit('Subir', array('class' => 'btn btn-primary')) }}
        </span>
    </div>

    {{ Form::input('hidden', 'idArticle', $article->id) }}

 {{ Form::close() }}