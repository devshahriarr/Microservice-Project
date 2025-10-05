import { useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import axiosClient from "../api/axiosClient";
import { AUTH_API } from "../api/apiConfig";

// export default function ProtectedRoute({ children }) {
//   const token = localStorage.getItem("token");

//   // if don't have any token then redirect to login
//   if (!token) {
//     return <Navigate to="/login" replace />;
//   }

//   // if user have the valid token then redirect to children
//   return children;
// }

export default function ProtectedRoute({ children }) {
  const [loading, setLoading] = useState(true);
  const [valid, setValid] = useState(false);

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      setValid(false);
      setLoading(false);
      return;
    }

    axiosClient
      .get(`${AUTH_API}/me`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then(() => {
        setValid(true);
      })
      .catch(() => {
        localStorage.removeItem("token");
        setValid(false);
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p>Checking authentication...</p>;
  if (!valid) return <Navigate to="/login" replace />;

  return children;
}