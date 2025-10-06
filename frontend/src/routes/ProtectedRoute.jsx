import { useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import { authApi } from "../api/authApi";

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

    authApi
      .getProfile()
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
