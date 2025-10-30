import { Outlet } from 'react-router-dom'
import Header from './Header'
import Footer from './Footer'

function Layout() {
  return (
    <div className="d-flex flex-column min-vh-100">
      <Header />
      <main className="flex-fill py-4">
        <div className="container">
          <Outlet />
        </div>
      </main>
      <Footer />
    </div>
  )
}

export default Layout
