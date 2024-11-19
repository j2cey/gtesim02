import EmployeeList from "../pages/employees/EmployeeList.vue";
import EmployeeForm from "../pages/employees/EmployeeForm.vue";

const employeesRoutes = [
    {
        path: '/employees',
        name: 'employees',
        component: EmployeeList,
    },
    {
        path: '/employees/:userid?/create',
        name: 'employees.create',
        component: EmployeeForm,
    },
    {
        path: '/employees/:id/edit',
        name: 'employees.edit',
        component: EmployeeForm,
    },
    {
        path: '/employees/:id/show',
        name: 'employees.show',
        component: EmployeeForm,
    }
];

export default employeesRoutes;
