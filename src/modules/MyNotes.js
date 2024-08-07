import $ from 'jquery';

class MyNotes {
    constructor() {
        this.events();
    }
    
    events() {
        $('.delete-note').on('click', this.deleteNote);
        $('.edit-note').on('click', this.editNote.bind(this));
        $('.update-note').on('click', this.updateNote.bind(this));
        $('.submit-note').on('click', this.createNote.bind(this));
    }

    editNote(e) {
        const thisNote = $(e.target).parents("li");
        if(thisNote.attr("state") == "editable") {
            this.cancelEdit(thisNote);
        } else {
            this.editAble(thisNote);
        }
    }

    editAble(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i> Cancel')
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
        thisNote.find(".update-note").addClass("update-note--visible");
        thisNote.attr("state", "editable");
    }

    cancelEdit(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit')
        thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field");
        thisNote.find(".update-note").removeClass("update-note--visible");
        thisNote.attr("state", "cancel");
    }

    updateNote(e) {
        const thisNote = $(e.target).parents("li");
        const dataNote = {
            "title" : thisNote.find(".note-title-field").val(),
            "content": thisNote.find(".note-body-field").val()
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: 'http://localhost:8080/wp-advanced/wp-json/wp/v2/note/'+thisNote.attr("data-id"),
            data: dataNote,
            method: 'POST',
            success: (res) => {
                this.cancelEdit(thisNote);
            },
            error: (err) => console.log(err)
        })
    }

    deleteNote(e) {
        const noteID = $(e.target).parents("li");
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: 'http://localhost:8080/wp-advanced/wp-json/wp/v2/note/'+noteID.attr("data-id"),
            method: 'DELETE',
            success: (res) => {
                noteID.slideUp();
            },
            error: (err) => console.log(err)
        })
    }

    createNote() {
        const dataNewNote = {
            "title": $('.new-note-title').val(),
            "content": $('.new-note-body').val(),
            "status": "publish"
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: 'http://localhost:8080/wp-advanced/wp-json/wp/v2/note/',
            data: dataNewNote,
            method: 'POST',
            success: (res) => {
                console.log(res)
                $('.new-note-title, .new-note-body').val("")
                $(`
                    <li data-id="${res.id}">
                        <input readonly class="note-title-field" value="${res.title.raw}">
                        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                        <textarea readonly class="note-body-field">${res.content.raw}</textarea>
                        <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                    </li>    
                `)
                .prependTo('#my-notes')
                .hide()
                .slideDown()
            },
            error: (err) => console.log(err)
        })
    }
}

export default MyNotes;