const mongoose = require('mongoose');

const studentGradeSchema = new mongoose.Schema({
    subject: {
        type: String,
        required: true
    },
    ticket_number: {
        type: Number,
        required: true
    },
    grade: {
        type: Number,
        required: true
    },
    teacher: {
        type: String,
        required: true
    },
    student: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Student',
        required: true
    }
});

module.exports = mongoose.model('StudentGrade', studentGradeSchema); 