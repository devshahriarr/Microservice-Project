import { createBrowserRouter } from "react-router-dom";
import HomePage from "../pages/home/HomePage";
import Login from "../pages/auth/login";
import Dashboard from "../pages/dashboards/Dashboard";
import Register from "../pages/auth/Register";
import ProtectedRoute from "./ProtectedRoute";
import Profile from "../pages/profile/Profile";
import Properties from "../pages/properties/Properties";

export const router = createBrowserRouter([
    {
        path: "/",
        element: <HomePage></HomePage>
    },
    {
        path: "/login",
        element: <Login></Login>
    },
    {
        path: "/register",
        element: <Register></Register>
    },
    {
        path: "/dashboard",
        element: (
            <ProtectedRoute>
                <Dashboard />
            </ProtectedRoute>
        ),
    },
    {
        path: "/profile",
        element: (
            <ProtectedRoute>
                <Profile />
            </ProtectedRoute>
        ),
    },
    {
        path: "/properties",
        element: (
            <ProtectedRoute>
                <Properties />
            </ProtectedRoute>
        ),
    },
])