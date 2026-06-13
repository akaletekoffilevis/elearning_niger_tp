<div class="card">
    <div class="card-header">
        <h3>Gestion du Quiz : <?= h($lesson['title']) ?></h3>
    </div>
    <div class="card-body">
        <form action="<?= Router::url('/admin/quiz/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="lesson_id" value="<?= h($lesson['id']) ?>">
            <div class="form-group">
                <label for="title">Titre du quiz</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= h($quiz['title'] ?? 'Quiz') ?>">
            </div>
            <div class="form-group">
                <label for="pass_score">Score de réussite (%)</label>
                <input type="number" name="pass_score" id="pass_score" class="form-control" value="<?= h($quiz['pass_score'] ?? '80') ?>" min="1" max="100">
            </div>

            <h3>Questions</h3>
            <div id="questions-container">
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $i => $q): ?>
                        <div class="question-block" style="border:1px solid var(--border);padding:1rem;margin-bottom:1rem;border-radius:var(--radius);">
                            <div class="form-group">
                                <label>Question <?= $i + 1 ?></label>
                                <input type="text" name="questions[<?= $i ?>][question]" class="form-control" value="<?= h($q['question']) ?>" required>
                                <input type="hidden" name="questions[<?= $i ?>][sort_order]" value="<?= $i ?>">
                            </div>
                            <div class="options-container">
                                <?php foreach ($q['options'] as $j => $opt): ?>
                                    <div class="form-inline" style="margin-bottom:0.5rem;">
                                        <input type="text" name="questions[<?= $i ?>][options][<?= $j ?>][text]" class="form-control" value="<?= h($opt['text']) ?>" placeholder="Option <?= $j + 1 ?>" required>
                                        <label style="white-space:nowrap;display:flex;align-items:center;gap:0.25rem;">
                                            <input type="radio" name="questions[<?= $i ?>][options][<?= $j ?>][is_correct]" value="1" <?= $opt['is_correct'] ? 'checked' : '' ?>> Correcte
                                        </label>
                                        <input type="hidden" name="questions[<?= $i ?>][options][<?= $j ?>][sort_order]" value="<?= $j ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-outline" onclick="addQuestion()">Ajouter une question</button>
            <button type="submit" class="btn btn-primary">Sauvegarder le quiz</button>
        </form>
    </div>
</div>

<script>
function addQuestion() {
    var container = document.getElementById('questions-container');
    var i = container.children.length;
    var div = document.createElement('div');
    div.className = 'question-block';
    div.style.cssText = 'border:1px solid var(--border);padding:1rem;margin-bottom:1rem;border-radius:var(--radius);';
    div.innerHTML = '<div class="form-group"><label>Question ' + (i + 1) + '</label><input type="text" name="questions[' + i + '][question]" class="form-control" required><input type="hidden" name="questions[' + i + '][sort_order]" value="' + i + '"></div>' +
        '<div class="options-container">' +
        '<div class="form-inline" style="margin-bottom:0.5rem;"><input type="text" name="questions[' + i + '][options][0][text]" class="form-control" placeholder="Option 1" required><label style="white-space:nowrap;display:flex;align-items:center;gap:0.25rem;"><input type="radio" name="questions[' + i + '][options][0][is_correct]" value="1"> Correcte</label><input type="hidden" name="questions[' + i + '][options][0][sort_order]" value="0"></div>' +
        '<div class="form-inline" style="margin-bottom:0.5rem;"><input type="text" name="questions[' + i + '][options][1][text]" class="form-control" placeholder="Option 2" required><label style="white-space:nowrap;display:flex;align-items:center;gap:0.25rem;"><input type="radio" name="questions[' + i + '][options][1][is_correct]" value="1"> Correcte</label><input type="hidden" name="questions[' + i + '][options][1][sort_order]" value="1"></div>' +
        '<div class="form-inline" style="margin-bottom:0.5rem;"><input type="text" name="questions[' + i + '][options][2][text]" class="form-control" placeholder="Option 3"><label style="white-space:nowrap;display:flex;align-items:center;gap:0.25rem;"><input type="radio" name="questions[' + i + '][options][2][is_correct]" value="1"> Correcte</label><input type="hidden" name="questions[' + i + '][options][2][sort_order]" value="2"></div>' +
        '<div class="form-inline" style="margin-bottom:0.5rem;"><input type="text" name="questions[' + i + '][options][3][text]" class="form-control" placeholder="Option 4"><label style="white-space:nowrap;display:flex;align-items:center;gap:0.25rem;"><input type="radio" name="questions[' + i + '][options][3][is_correct]" value="1"> Correcte</label><input type="hidden" name="questions[' + i + '][options][3][sort_order]" value="3"></div>' +
        '</div>';
    container.appendChild(div);
}
</script>
