import { useEffect, useState } from "react";
import axiosClient from "../../api/axiosClient";
import { CRUD_API } from "../../api/apiConfig";
import { useNavigate } from "react-router-dom";
import Navbar from "../../components/navbar/Navbar";

export default function Dashboard() {
  const navigate = useNavigate();
  const [properties, setProperties] = useState([]);

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      navigate("/login");
      return;
    }

    axiosClient
      .get(`${CRUD_API}/properties`)
      .then((res) => setProperties(res.data.data || []))
      .catch((err) => {
        if (err.response?.status === 401) {
          localStorage.removeItem("token");
          navigate("/login");
        }
      });
  }, []);

  return (
    <div>
      <Navbar></Navbar>
      <div className="p-6">
        <h2 className="text-2xl mb-4">Dashboard</h2>
        <ul className="space-y-2">
          {properties.map((p) => (
            <li key={p.id} className="border p-3 rounded">
              <strong>{p.project_name}</strong> — {p.status_type} ($
              {p.asking_price})
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}
