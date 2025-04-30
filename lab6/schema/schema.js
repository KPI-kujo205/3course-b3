const { GraphQLSchema, GraphQLObjectType, GraphQLString, GraphQLID, GraphQLInt, GraphQLList, GraphQLNonNull } = require('graphql');
const Student = require('../models/Student');
const StudentGrade = require('../models/StudentGrade');

const StudentType = new GraphQLObjectType({
    name: 'Student',
    fields: () => ({
        id: { type: GraphQLID },
        name: { type: GraphQLString },
        surname: { type: GraphQLString },
        group: { type: GraphQLString },
        grades: {
            type: new GraphQLList(GradeType),
            resolve(parent, args) {
                return StudentGrade.find({ student: parent.id });
            }
        }
    })
});

const GradeType = new GraphQLObjectType({
    name: 'Grade',
    fields: () => ({
        id: { type: GraphQLID },
        subject: { type: GraphQLString },
        ticket_number: { type: GraphQLInt },
        grade: { type: GraphQLInt },
        teacher: { type: GraphQLString },
        student: {
            type: StudentType,
            resolve(parent, args) {
                return Student.findById(parent.student);
            }
        }
    })
});

const RootQuery = new GraphQLObjectType({
    name: 'RootQueryType',
    fields: {
        student: {
            type: StudentType,
            args: { id: { type: GraphQLID } },
            resolve(parent, args) {
                return Student.findById(args.id);
            }
        },
        students: {
            type: new GraphQLList(StudentType),
            resolve(parent, args) {
                return Student.find({});
            }
        },
        grade: {
            type: GradeType,
            args: { id: { type: GraphQLID } },
            resolve(parent, args) {
                return StudentGrade.findById(args.id);
            }
        },
        grades: {
            type: new GraphQLList(GradeType),
            resolve(parent, args) {
                return StudentGrade.find({});
            }
        }
    }
});

const Mutation = new GraphQLObjectType({
    name: 'Mutation',
    fields: {
        addStudent: {
            type: StudentType,
            args: {
                name: { type: new GraphQLNonNull(GraphQLString) },
                surname: { type: new GraphQLNonNull(GraphQLString) },
                group: { type: new GraphQLNonNull(GraphQLString) }
            },
            resolve(parent, args) {
                const student = new Student({
                    name: args.name,
                    surname: args.surname,
                    group: args.group
                });
                return student.save();
            }
        },
        addGrade: {
            type: GradeType,
            args: {
                subject: { type: new GraphQLNonNull(GraphQLString) },
                ticket_number: { type: new GraphQLNonNull(GraphQLInt) },
                grade: { type: new GraphQLNonNull(GraphQLInt) },
                teacher: { type: new GraphQLNonNull(GraphQLString) },
                studentId: { type: new GraphQLNonNull(GraphQLID) }
            },
            resolve(parent, args) {
                const grade = new StudentGrade({
                    subject: args.subject,
                    ticket_number: args.ticket_number,
                    grade: args.grade,
                    teacher: args.teacher,
                    student: args.studentId
                });
                return grade.save();
            }
        },
        updateStudent: {
            type: StudentType,
            args: {
                id: { type: new GraphQLNonNull(GraphQLID) },
                name: { type: GraphQLString },
                surname: { type: GraphQLString },
                group: { type: GraphQLString }
            },
            resolve(parent, args) {
                return Student.findByIdAndUpdate(
                    args.id,
                    {
                        $set: {
                            name: args.name,
                            surname: args.surname,
                            group: args.group
                        }
                    },
                    { new: true }
                );
            }
        },
        deleteStudent: {
            type: StudentType,
            args: {
                id: { type: new GraphQLNonNull(GraphQLID) }
            },
            resolve(parent, args) {
                return Student.findByIdAndRemove(args.id);
            }
        }
    }
});

module.exports = new GraphQLSchema({
    query: RootQuery,
    mutation: Mutation
}); 