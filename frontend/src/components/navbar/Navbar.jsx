import { Link, useNavigate } from "react-router-dom";

const Navbar = () => {
  const navigate = useNavigate();
  const token = localStorage.getItem("token");

  const handleLogout = () => {
    localStorage.removeItem("token");
    navigate("/login");
  };

  return (
    <nav className="bg-gray-900 text-white p-4 shadow-md flex justify-between items-center">
      {/* Left side — Logo */}
      <div>
        {/* <Link
          to="/"
          className="text-xl font-bold hover:text-blue-400 transition"
        >
          PropertyApp
        </Link> */}
      </div>

      {/* Right side — Links */}
      <div className="space-x-6 flex items-center">
        <Link to="/" className="hover:text-blue-400 transition">
          Home
        </Link>

        {token ? (
          <>
            <Link to="/dashboard" className="hover:text-blue-400 px-4 transition">
              Dashboard
            </Link>
            <Link to="/properties" className="hover:text-blue-400 px-3 transition">
              Properties
            </Link>
            <Link to="/profile" className="hover:text-blue-400 px-3 transition">
              Profile
            </Link>

            <button
              onClick={handleLogout}
              className="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md transition"
            >
              Logout
            </button>
          </>
        ) : (
          <>
            <Link to="/login" className="hover:text-blue-400 transition">
              Login
            </Link>
            <Link to="/register" className="hover:text-blue-400 transition">
              Register
            </Link>
          </>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
