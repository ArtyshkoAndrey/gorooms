<div class="form-group">
  <label for="header">Заголовок</label>
  <input type="text" name="header" id="header" value="{{ old('header') ?? @$instruction->header ?? '' }}"
         class="form-control " required/>
</div>
<div class="form-group">
  <label for="name">Основной текст</label>
  <textarea name="content" id="content" cols="30" rows="10">
    {{ old('content') ?? @$instruction->content ?? '' }}
  </textarea>
</div>