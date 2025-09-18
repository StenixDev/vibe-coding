import { useState } from "react";

const initialFriends = [
  {
    id: 118836,
    name: "Clark",
    image: "https://i.pravatar.cc/48?u=118836",
    balance: -7,
  },
  {
    id: 933372,
    name: "Sarah",
    image: "https://i.pravatar.cc/48?u=933372",
    balance: 20,
  },
  {
    id: 499476,
    name: "Anthony",
    image: "https://i.pravatar.cc/48?u=499476",
    balance: 0,
  },
];


function Button({children, onClick}){
  return <button onClick={onClick} className="button">{children}</button>
}


function App(){
  const [friends, setFriends] = useState(initialFriends)
  const [showAddFriend,  setShowAddFriend] = useState(false)
  const [selectedFriend, setSelectedFriend] = useState(null)

  function handleShowAddFriend() {
    setShowAddFriend(show => !show)
    setSelectedFriend(null)
    
  }

  function handleAddFriend(friend){
    setFriends(friends => [...friends, friend])
    setShowAddFriend(false)
  }

  function handleSelectFriend(friend){
    //setSelectedFriend(friend)
    setSelectedFriend(cur => cur?.id === friend.id ? null : friend)
    setShowAddFriend(false)
  }


  return <div className="app">
    <div className="sidebar">
      <FriendsList friends={friends} selectedFriend={selectedFriend} onSelectedFriend={handleSelectFriend} />
      
      
      { showAddFriend && <FormAddFriend onAddFriends={handleAddFriend} /> }


      <Button onClick={handleShowAddFriend}>
        {showAddFriend ? 'Close' : 'Add Friend'}
        </Button>
    </div>

   {selectedFriend && <FormSplitBill selectedFriend={selectedFriend} /> }
    
  </div>
}

function FriendsList({friends, onSelectedFriend, selectedFriend}){


  return <ul>

    {
      friends.map(friend =>  <Friend selectedFriend={selectedFriend} onSelectedFriend={onSelectedFriend} friend={friend} key={friend.id} /> )
    }
    
  </ul>
}

function Friend({friend, onSelectedFriend, selectedFriend}){

  const isSelected = selectedFriend?.id === friend.id


  return <li className={isSelected ? 'selected' : ''}>

    <img src={friend.image} alt={friend.name} />
    <h3>{friend.name}</h3>

    {friend.balance < 0 && <p className="red">You owe {friend.name} ${Math.abs(friend.balance)}</p>}
    {friend.balance > 0 && <p className="green">{friend.name} owes you ${Math.abs(friend.balance)}</p>}
    {friend.balance === 0 && <p>You and your {friend.name} are even</p>}

    
      <Button onClick={() => onSelectedFriend(friend)} className="button">
        { isSelected ? 'Close' : 'Select' }
    </Button>
    

    
  </li>
}



function FormAddFriend({onAddFriends}){

  const [name, setName] = useState('')
  const [image, setImage] = useState('https://i.pravatar.cc/48')

  function handleSubmit(e){
    e.preventDefault()
    const id = crypto.randomUUID()

    if(!name || !image) return

    const newFriend = {
      id: id,
      name: name,
      image: `${image}?=${id}`,
      balance: 0,
    }

    onAddFriends(newFriend)


    setName('')
    setImage('https://i.pravatar.cc/48')
  }


  return  <form className="form-add-friend" onSubmit={handleSubmit}>
    <label>👩🏻‍🤝‍🧑🏻 Friend name</label>
    <input type="text" value={name} onChange={(e) => setName(e.target.value)} />

    <label>🌐 Image URL</label>
    <input type="text" value={image} onChange={(e) => setImage(e.target.value)} />

    <Button className="button">Add</Button>
  </form>
  
}


function FormSplitBill({selectedFriend}){
  return <form className="form-split-bill">
    <h2>Split a bill with {selectedFriend.name}</h2>

    <label>💲 Bill Value</label>
    <input type="text" />

    <label>💲 Your Expense</label>
    <input type="text" />

    <label>💲 {selectedFriend.name}'s expense</label>
    <input type="text" disabled />

    <label>💰 Who is paying the bill</label>
    <select>
      <option value='user'>You</option>
      <option value='friend'>X</option>
    </select>

    <Button className="button">Split Bill</Button>

  </form>
}

export default App